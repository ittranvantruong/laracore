<div class="col-12 col-md-9">
    <div class="card">
        <div class="card-body">
            <div class="row">
                <!-- name -->
                <div class="col-12">
                    <div class="mb-3">
                        <label class="form-label">@lang('name')</label>
                        <x-input name="name" :value="old('name')" :required="true" placeholder="{{ __('Tên') }}" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
