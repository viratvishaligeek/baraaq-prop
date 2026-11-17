@php
    $animateContent = $sectionConfig['animate_content'] ?? true;
    $visibility = trim((!($sectionConfig['show_mobile'] ?? true) ? ' d-none d-md-block' : '').(!($sectionConfig['show_tablet'] ?? true) ? ' d-md-none d-lg-block' : '').(!($sectionConfig['show_desktop'] ?? true) ? ' d-lg-none' : ''));
@endphp

<div class="download-sec-3 {{ $sectionConfig['vertical_spacing'] ?? 'space-top' }} {{ $visibility }}">
    <div class="download-3-bg-inner" @if(!empty($sectionConfig['background_color'])) style="background-color: {{ $sectionConfig['background_color'] }};" @endif @if(!empty($sectionConfig['background_image'])) data-bg-src="{{ $sectionConfig['background_image'] }}" @endif>
        <div class="container">
            <div class="row gy-4 align-items-center">
                <div class="col-lg-5 col-xl-5 col-xxl-6">
                    <div class="download-3-left">
                        <div class="map-icon-box">
                            <a href="{{ $sectionConfig['map_url'] ?? route('properties.index') }}">
                                @if(!empty($sectionConfig['map_image']))<img src="{{ $sectionConfig['map_image'] }}" alt="Explore properties">@endif
                            </a>
                        </div>
                        <div class="content-box">
                            @if(!empty($sectionConfig['icon_image']))<img src="{{ $sectionConfig['icon_image'] }}" alt="Property search">@endif
                            <h4 class="box-title">{{ $sectionConfig['map_heading'] ?? '' }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7 col-xl-7 col-xxl-6">
                    <div class="download-get-in-touch">
                        <form action="{{ route('enquiries.store') }}" method="POST" class="homepage-property-enquiry">
                            @csrf
                            <div class="title-area text-left mb-70">
                                <p class="sub-title{{ $animateContent ? ' text-anime-style-2' : '' }}"><span class="double-line"></span>{{ $sectionConfig['eyebrow'] ?? '' }}</p>
                                <h2 class="sec-title{{ $animateContent ? ' text-anime-style-3' : '' }}">{{ $sectionConfig['heading'] ?? '' }}</h2>
                            </div>
                            <div class="row">
                                <div class="form-group col-lg-6 col-md-6">
                                    <label for="home3-enquiry-name-{{ $section->id }}">{{ $sectionConfig['name_label'] ?? 'Name*' }}</label>
                                    <input type="text" class="form-control" name="name" id="home3-enquiry-name-{{ $section->id }}" placeholder="{{ $sectionConfig['name_placeholder'] ?? '' }}" value="{{ old('name') }}" required>
                                </div>
                                <div class="form-group col-lg-6 col-md-6">
                                    <label for="home3-enquiry-email-{{ $section->id }}">{{ $sectionConfig['email_label'] ?? 'Email*' }}</label>
                                    <input type="email" class="form-control" name="email" id="home3-enquiry-email-{{ $section->id }}" placeholder="{{ $sectionConfig['email_placeholder'] ?? '' }}" value="{{ old('email') }}" required>
                                </div>
                                <div class="form-group col-lg-6 col-md-6">
                                    <label for="home3-enquiry-date-{{ $section->id }}">{{ $sectionConfig['date_label'] ?? 'Desired Date' }}</label>
                                    <input type="date" class="form-control" name="preferred_date" id="home3-enquiry-date-{{ $section->id }}" min="{{ now()->toDateString() }}" value="{{ old('preferred_date') }}">
                                </div>
                                <div class="form-group col-lg-6 col-md-6">
                                    <label for="home3-enquiry-time-{{ $section->id }}">{{ $sectionConfig['time_label'] ?? 'Desired Time' }}</label>
                                    <input type="text" class="form-control" name="preferred_time" id="home3-enquiry-time-{{ $section->id }}" placeholder="{{ $sectionConfig['time_placeholder'] ?? 'Desired Time' }}" value="{{ old('preferred_time') }}">
                                </div>
                                <div class="form-group col-lg-12">
                                    <label for="home3-enquiry-message-{{ $section->id }}">{{ $sectionConfig['message_label'] ?? 'Additional Message' }}</label>
                                    <textarea name="message" id="home3-enquiry-message-{{ $section->id }}" cols="30" rows="3" class="form-control" placeholder="{{ $sectionConfig['message_placeholder'] ?? '' }}">{{ old('message') }}</textarea>
                                </div>
                                <div class="form-btn col-12 text-end"><button type="submit" class="th-btn bg-theme radius">{{ $sectionConfig['submit_label'] ?? 'Submit Now' }}</button></div>
                            </div>
                            @if(session('enquiry_success'))<p class="form-messages mb-0 mt-3 text-success">{{ session('enquiry_success') }}</p>@endif
                            @if($errors->any())<div class="form-messages mb-0 mt-3 text-danger">{{ $errors->first() }}</div>@endif
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
