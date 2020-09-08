<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PagesStaticController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth', ['except' => 'index']);
    }

    public function palanca()
    {
        return view('pages.static.palanca');
    }

    public function preweekend()
    {
        return view('pages.static.preweekend');
    }

    public function postweekend()
    {
        return view('pages.static.postweekend');
    }

    public function secretariat()
    {
        $secretariat = \App\Models\Secretariat::first();

        return view('pages.static.secretariat', ['s' => $secretariat]);
    }

    public function secuelas()
    {
        return view('pages.static.secuelas');
    }

    public function sponsoring()
    {
        return view('pages.static.sponsoring');
    }

    public function reuniongroups()
    {
        return view('pages.static.reuniongroups');
    }

    public function weekendcommittee()
    {
        return view('pages.static.weekendactivities');
    }

    public function vocabulary()
    {
        return view('pages.static.vocabulary');
    }

    public function helpLeadersTools()
    {
        return view('pages.static.help_leaders_tools');
    }





    /////


    public function teamguide()
    {
        return view('teamguide.main');
    }

    public function cha_general_instructions()
    {
        return view('teamguide.chageneralinstructions');
    }

    public function packinglist()
    {
        return view('teamguide.teammember_packinglist');
    }

    public function meditation()
    {
        return view('teamguide.meditation');
    }

    public function decolores()
    {
        return view('teamguide.decolores_words');
    }

    public function palancaSampleIndividualLetter()
    {
        return view('pages.static.palanca-sample-individual-letter');
    }

    public function palancaSampleGeneralLetter()
    {
        return view('pages.static.palanca-sample-general-letter');
    }

    public function table_rollo_discussion()
    {
        return view('teamguide.table_rollo_discussion');
    }

    public function relationship_of_talks()
    {
        return view('teamguide.relationship_of_talks');
    }
}
