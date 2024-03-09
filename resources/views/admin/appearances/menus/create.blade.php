@extends('admin.layouts.master')
@push('libs-css')

@endpush
@section('content')
    <div class="page-body">
        <div class="container-xl">
            <x-form :action="route('admin.appearance.menu.store')" type="post" :validate="true">
                <div class="row justify-content-center">
                    @include('admin.appearances.menus.forms.create-left')
                    @include('admin.appearances.menus.forms.create-right')
                </div>
                @include('admin.forms.actions-fixed')
            </x-form>
        </div>
    </div>
@endsection

@push('libs-js')

@endpush

@push('custom-js')

@endpush
