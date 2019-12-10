@extends('layouts.app')

@section('title')
    Packing List - {{ config('site.community_acronym') }}
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <h3>Team Member Weekend Packing List</h3>

                <ul>
                    <li>Job Description</li>
                    <li>Bed Linens or Sleeping Bag, Pillow</li>
                    <li>Towels & Washcloth</li>
                    <li>Sundries: shampoo, soap, toothbrush, toothpaste, etc</li>
                    <li>Casual Clothes and Extra Shoes</li>
                    <li>Sleepwear</li>
                    <li>Plastic Bag for Dirty/Wet Clothes</li>
                    <li>Prescription Medication</li>
                    <li>Flashlight</li>
                    <li>Umbrella</li>
                    <li>Tres Dias Cross and Pilgrim's Guide</li>
                    <li>Jacket, Coat or Sweater</li>
                    <li>Rollistas (& back-ups):
                        <ul>
                            <li>Dress Clothes</li>
                            <li>Rollo, Handouts (and any "props")</li>
                        </ul>
                    </li>
                    <li>Dorm Chas
                        <ul>
                            <li>Alarm Clock</li>
                        </ul>
                    </li>
                    <li>Palanca</li>
                    <li>Food For Thursday Night Reception</li>
                    <li>A servant's heart, a rested body, a spirit of cooperation, flexibility and lots of love!</li>
                </ul>
                <p><strong>Note:</strong></p>
                <ul>
                    <li>Please DO NOT bring radio, TV, or cameras.</li>
                    <li>Please DO NOT bring a cell phone or pager unless absolutely necessary, keep it out of sight of the Candidates and set it on "Silent Mode". It must never be used in or near the Rollo Room or Chapel.</li>
                </ul>
            </div>

        </div>
    </div>
@endsection
