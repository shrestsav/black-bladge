<?php

namespace App\Http\Controllers\Api\Customer;

use App\AppDefault;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Customer\LegalDocs as LegalDocsResource;

class PageController extends Controller
{
    public function legalDocs()
    {
        $docs = AppDefault::firstOrFail();

        return new LegalDocsResource($docs);
    }
}
