<?php

namespace App\Http\Controllers;

use App\Models\Site;
use Illuminate\Http\Request;
use App\Http\Requests\SiteStoreRequest;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\SiteUpdateRequest;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ZipArchive;

class SiteController extends Controller
{
    public const MIME_ALLOWLIST = [
        'text/html',
        'text/css',
        'text/javascript',
        'image/png',
        'image/jpeg',
        'image/gif',
        'image/svg+xml',
        'image/webp',
        'application/json',
        //'application/pdf',
        'text/plain',
        'font/woff',
        'font/woff2',
        'font/ttf',
    ];

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('view-any', Site::class);

        $search = $request->get('search', '');

        $sites = Site::search($search)
            ->latest()
            ->paginate(5)
            ->withQueryString();

        return view('app.sites.index', compact('sites', 'search'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $this->authorize('create', Site::class);

        return view('app.sites.create');
    }

    /**
     * @param string $zipPath
     * @return string
     */
    public static function getSitePathFromZip(string $zipPath)
    {
        return rtrim($zipPath, '.zip').'/';
    }

    /**
     * @param string $path
     * @return bool
     */
    private static function removeSite(string $zipPath)
    {
        $fullPath = Storage::path($zipPath);
        $dir = self::getSitePathFromZip($fullPath);

        if (!is_dir($dir))
            return;
            
        // Source: https://stackoverflow.com/a/3349792
        $it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new RecursiveIteratorIterator($it,
                    RecursiveIteratorIterator::CHILD_FIRST);
        foreach($files as $file) {
            if ($file->isDir()){
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }
        rmdir($dir);
    }

    /**
     * @param string $path
     * @return bool|array
     */
    private static function extractSite(string $zipPath, bool $allowUnsafe = false)
    {
        $fullPath = Storage::path($zipPath);
        $archive = new ZipArchive;
        $result = $archive->open($fullPath);
        $filter = null;

        if(!$allowUnsafe){
            $filter = [];

            for($i = 0; $i < $archive->numFiles; $i++){
                $file = $archive->statIndex($i);
                     
                $mimeType =  mime_content_type('zip://' . $archive->filename . '#' . $file['name']);

                if(!in_array(strtolower($mimeType), self::MIME_ALLOWLIST)){
                    continue;
                }

                $filter[] = $file['name'];
            }
        }

        if ($result !== TRUE)
            return false;

        $archive->extractTo(self::getSitePathFromZip($fullPath), $filter);
        $archive->close();

        return $filter;
    }

    /**
     * @param \App\Http\Requests\SiteStoreRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(SiteStoreRequest $request)
    {
        $this->authorize('create', Site::class);

        $validated = $request->validated();
        $succesfullyExtracted = [];

        if ($request->hasFile('path_nl')) {
            $validated['path_nl'] = $request->file('path_nl')->store('public');
            
            if(($extracted = self::extractSite($validated['path_nl'], $request->allow_unsafe)) !== false){
                array_push($succesfullyExtracted, $extracted);
            }
        }

        if ($request->hasFile('path_en')) {
            $validated['path_en'] = $request->file('path_en')->store('public');
            
            if(($extracted = self::extractSite($validated['path_en'], $request->allow_unsafe)) !== false){
                array_push($succesfullyExtracted, $extracted);
            }
        }

        $request->user()->sites()->create($validated);

        return redirect()
            ->route('sites.index')
            ->withSuccess(__('crud.common.created'))
            ->withDebug($succesfullyExtracted);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function random(Request $request)
    {
        $site = Site::inRandomOrder()->first();

        if($site === null) {
            echo 'Er zijn momenteel geen sites beschikbaar.';
            exit;
        }

        return $this->show($request, $site);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function randomEnglish(Request $request)
    {
        $site = Site::whereNotNull('path_en')->inRandomOrder()->first();

        if($site === null) {
            echo 'Er zijn momenteel geen engelstalige sites beschikbaar.';
            exit;
        }

        return $this->show($request, $site)
            ->withEnglishLanguage(true);
    }
    
    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Site $site
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Site $site)
    {
        $this->authorize('view', $site);

        return view('app.sites.show', compact('site'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Site $site
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, Site $site)
    {
        $this->authorize('update', $site);

        return view('app.sites.edit', compact('site'));
    }

    /**
     * @param \App\Http\Requests\SiteUpdateRequest $request
     * @param \App\Models\Site $site
     * @return \Illuminate\Http\Response
     */
    public function update(SiteUpdateRequest $request, Site $site)
    {
        $this->authorize('update', $site);

        $validated = $request->validated();
        $succesfullyExtracted = [];
        
        if ($request->hasFile('path_nl')) {
            if ($site->path_nl) {
                Storage::delete($site->path_nl);
                self::removeSite($site->path_nl);
            }

            $validated['path_nl'] = $request->file('path_nl')->store('public');
            
            if(($extracted = self::extractSite($validated['path_nl'], $request->allow_unsafe)) !== false){
                array_push($succesfullyExtracted, $extracted);
            }
        }

        if ($request->hasFile('path_en')) {
            if ($site->path_en) {
                Storage::delete($site->path_en);
                self::removeSite($site->path_en);
            }

            $validated['path_en'] = $request->file('path_en')->store('public');

            if(($extracted = self::extractSite($validated['path_en'], $request->allow_unsafe)) !== false){
                array_push($succesfullyExtracted, $extracted);
            }
        }

        $site->update($validated);

        return redirect()
            ->route('sites.index', $site)
            ->withSuccess(__('crud.common.saved'))
            ->withDebug($succesfullyExtracted);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Site $site
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Site $site)
    {
        $this->authorize('delete', $site);

        if ($site->path_nl) {
            Storage::delete($site->path_nl);
            self::removeSite($site->path_nl);
        }

        if ($site->path_en) {
            Storage::delete($site->path_en);
            self::removeSite($site->path_en);
        }

        $site->delete();

        return redirect()
            ->route('sites.index')
            ->withSuccess(__('crud.common.removed'));
    }
}
