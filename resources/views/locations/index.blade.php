@extends('layouts.app')

@section('title', 'Community Meeting Locations')

@section('content')
  <div class="container">
    <div class="row">
      <div class="col-12 mb-3">

          <div class="card">
          <div class="card-header card-title">Locations
            @can('create events')
              <div class="btn-group float-right" role="group" aria-label="Add Button">
                <a href="{{route('location.create')}}"><button type="button" class="btn btn-primary"><i class="fa fa-btn fa-plus"></i>Add</button></a>
              </div>
            @endcan
          </div>


          <table class="table table-hover table-striped">
              @foreach ($locations as $location)
                  <tr>
                      <td>
                          {{$location->location_name}} <a href="{{route('location.edit', $location->id)}}"class="badge badge-pill badge-secondary ml-3"> Edit </a><br>
                          <a href="{{$location->location_url}}" title="{{ $location->location_url }}">{{ substr($location->location_url, 0, 40) }}</a><br>
                          <a href="{{$location->map_url_link}}" title="{{ $location->map_url_link }}">{{ substr($location->map_url_link, 0, 40) . '...' }}</a>
                      </td>
                      <td>
                          {{$location->address_street}}<br>
                          {{$location->address_city}}, {{$location->address_province}}<br>
                          {{$location->address_postal}}
                      </td>
                      <td>
                          {{$location->contact_name}}<br>
                          {{$location->contact_email}}<br>
                          {{$location->contact_phone}}
                      </td>
                  </tr>
              @endforeach
          </table>

        </div>
      </div>

    </div>
  </div>

@endsection
