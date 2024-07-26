<?php

namespace App\Http\Controllers;

use App\Http\Requests\Document\StoreDocumentREquest;
use App\Http\Requests\Document\UpdateDocumentREquest;
use App\Http\Resources\DocumentREsource;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\PhpWord;

class DocumentController extends Controller
{
    public function index(Request $request)
    {

        $documentsQuery = Document::query();

        $this->applySearch($documentsQuery, $request->search);

        $documents = DocumentResource::collection($documentsQuery->paginate(5));
        return inertia('Documents/Index',[
            'documents' => $documents,
            'search' => $request->search ?? '',
        ]);
    }

    protected function applySearch($query, $search)
    {
        return $query->when($search, function ($query, $search) {
            $query->where('title', 'like', '%' . $search . '%');
        });
    }

    public function create()
    {
        return inertia('Documents/Create');
    }

    public function store(StoreDocumentRequest $request)
    {
        $validated = $request->validated();
        $phpWord = new PhpWord();
        $section = $phpWord->addSection();

        $section->addText('Title: ' . $validated['title']);
        $section->addText('Date: ' . $validated['date']);

        $fileName = 'document_' . time() . '.docx';
        $tempFilePath = storage_path('app/public/documents/' . $fileName);
        $phpWord->save($tempFilePath, 'Word2007');

        $finalFilePath = storage_path('app/public/documents/' . $fileName);


        if (!file_exists(dirname($finalFilePath))) {
            mkdir(dirname($finalFilePath), 0755, true);
        }
        rename($tempFilePath, $finalFilePath);

        $validated['file'] = $fileName;

        Document::create($validated);

        return redirect()->route('documents.index');
    }


    public function download(Document $document)
    {

        if (!$document->file || !Storage::exists('public/documents/' . $document->file)) {
            abort(404, 'File not found.');
        }

        return Storage::download('public/documents/' . $document->file);
    }


    public function destroy(Document $document)
    {

        if ($document->file) {
            Storage::delete('public/documents/' . $document->file);
        }

        $document->delete();

        return redirect()->route('documents.index');
    }

}
