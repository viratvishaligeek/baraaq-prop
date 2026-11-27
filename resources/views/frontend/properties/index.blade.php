@extends('frontend.layouts.theme')
@section('title', \App\Models\Setting::read('properties_meta_title', 'Properties | ' . config('app.name')))
@section('meta_description',
    \App\Models\Setting::read(
    'properties_meta_description',
    'Browse published property
    listings.',
    ))
@section('canonical_url', route('properties.index'))
@section('content')
    <div class="breadcumb-wrapper" data-bg-src="{{ asset('theme-assets/img/blog/breadcrumb-bg.jpg') }}">
        <div class="container">
            <div class="breadcumb-content">
                <h1 class="breadcumb-title">Properties</h1>
                <ul class="breadcumb-menu">
                    <li><a href="{{ route('home') }}">Home</a></li>
                    <li>Properties</li>
                </ul>
            </div>
        </div>
    </div>
    <div class="search-area">
        <form action="https://html.themehour.net/submit-form">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-xxl-10">
                        <div class="search-wrap">
                            <div class="row">
                                <div class="search-wrap-area-top">
                                    <ul class="nav nav-tabs custom" id="myTab" role="tablist">
                                        <li class="nav-item" role="presentation"><button class="nav-link active"
                                                id="buy-tab" data-bs-toggle="tab" data-bs-target="#buy" type="button"
                                                role="tab" aria-controls="buy" aria-selected="true">Buy</button></li>
                                        <li class="nav-item" role="presentation"><button class="nav-link" id="sell-tab"
                                                data-bs-toggle="tab" data-bs-target="#sell" type="button" role="tab"
                                                aria-controls="sell" aria-selected="false">Sell</button></li>
                                        <li class="nav-item" role="presentation"><button class="nav-link" id="rent-tab"
                                                data-bs-toggle="tab" data-bs-target="#rent" type="button" role="tab"
                                                aria-controls="rent" aria-selected="false">Rent</button></li>
                                    </ul>
                                </div>
                                <div class="tab-content" id="myTabContent">
                                    <div class="tab-pane fade show active" id="buy" role="tabpanel"
                                        aria-labelledby="buy-tab">
                                        <div class="select-group-wrapper">
                                            <div class="form-group"><label for="property_type">Property Type</label>
                                                <select name="property_type" id="property_type"
                                                    class="form-select nice-select">
                                                    <option value="" disabled="disabled" selected="selected" hidden>
                                                        Select Property Type</option>
                                                    <option value="apartment">Apartment</option>
                                                    <option value="house">House</option>
                                                    <option value="villa">Villa</option>
                                                </select>
                                            </div>
                                            <div class="form-group"><label for="room_type">Room Type</label> <select
                                                    name="room_type" id="room_type" class="form-select nice-select">
                                                    <option value="" disabled="disabled" selected="selected" hidden>
                                                        Select Room Type</option>
                                                    <option value="single">Single Room</option>
                                                    <option value="double">Double Room</option>
                                                    <option value="suite">Suite</option>
                                                </select></div>
                                            <div class="form-group"><label for="mini_area">Mini Area (Sqft)</label>
                                                <select name="mini_area" id="mini_area" class="form-select nice-select">
                                                    <option value="" disabled="disabled" selected="selected" hidden>
                                                        Select Area</option>
                                                    <option value="500">500 Sqft</option>
                                                    <option value="1000">1000 Sqft</option>
                                                    <option value="1500">1500 Sqft</option>
                                                </select>
                                            </div>
                                            <div class="form-group"><label for="max_area">Max Area (Sqft)</label>
                                                <select name="max_area" id="max_area" class="form-select nice-select">
                                                    <option value="" disabled="disabled" selected="selected" hidden>
                                                        Select Max Area</option>
                                                    <option value="2000">2000 Sqft</option>
                                                    <option value="3000">3000 Sqft</option>
                                                    <option value="4000">4000 Sqft</option>
                                                </select>
                                            </div>
                                            <div class="advance-btn-wrapper">
                                                <div class="advance-search-btn advance-search-active">
                                                    <div class="search-btn-item"><a
                                                            class="th-btn radius outline w-100">Advanced Search <i
                                                                class="fa-solid fa-sliders"></i></a></div>
                                                </div>
                                            </div>
                                            <div class="advance-search-wrapper">
                                                <div class="form-group"><label for="Amenities">Amenities</label> <select
                                                        name="Amenities" id="Amenities" class="form-select nice-select">
                                                        <option value="1" disabled="disabled" selected="selected"
                                                            hidden>Select Amenities</option>
                                                        <option value="2">Gymnasium</option>
                                                        <option value="3">Swiming Pool</option>
                                                    </select></div>
                                                <div class="form-group"><label for="Garage">Garage</label> <select
                                                        name="Garage" id="Garage" class="form-select nice-select">
                                                        <option value="" disabled="disabled" selected="selected"
                                                            hidden>
                                                            Select Garage</option>
                                                        <option value="1">Small Garage</option>
                                                        <option value="2">Large Garage</option>
                                                        <option value="3">Extra Large Garage</option>
                                                    </select></div>
                                                <div class="form-group"><label for="Floor_Level">Floor Level</label>
                                                    <select name="Floor_Level" id="Floor_Level"
                                                        class="form-select nice-select">
                                                        <option value="Floor_Level" disabled="disabled"
                                                            selected="selected" hidden>Select Floor Level</option>
                                                        <option value="1">Ground Floor</option>
                                                        <option value="2">Middle Floor</option>
                                                        <option value="3">Top Floor</option>
                                                    </select>
                                                </div>
                                                <div class="form-group"><label for="parking_spaces">Parking
                                                        Spaces</label> <select name="parking_spaces" id="parking_spaces"
                                                        class="form-select nice-select">
                                                        <option value="" disabled="disabled" selected="selected"
                                                            hidden>
                                                            Select Parking Spaces</option>
                                                        <option value="1">No Parking</option>
                                                        <option value="2">2 Space</option>
                                                        <option value="3">3 Spaces</option>
                                                    </select></div>
                                            </div>
                                            <div class="form-group"><label for="max_bedrooms1">Max of Bedrooms</label>
                                                <select name="max_bedrooms" id="max_bedrooms1"
                                                    class="form-select nice-select">
                                                    <option value="" disabled="disabled" selected="selected"
                                                        hidden>
                                                        Select Max Bedrooms</option>
                                                    <option value="1">1 Bedroom</option>
                                                    <option value="2">2 Bedrooms</option>
                                                    <option value="3">3 Bedrooms</option>
                                                </select>
                                            </div>
                                            <div class="form-group"><label for="max_bedrooms">Max of Bedrooms</label>
                                                <select name="max_bedrooms" id="max_bedrooms"
                                                    class="form-select nice-select">
                                                    <option value="" disabled="disabled" selected="selected"
                                                        hidden>
                                                        Select Max Bedrooms</option>
                                                    <option value="1">1 Bedroom</option>
                                                    <option value="2">2 Bedrooms</option>
                                                    <option value="3">3 Bedrooms</option>
                                                </select>
                                            </div>
                                            <div class="form-group"><label for="location">Location</label> <select
                                                    name="location" id="location" class="form-select nice-select">
                                                    <option value="" disabled="disabled" selected="selected"
                                                        hidden>
                                                        Select Location</option>
                                                    <option value="new_york">New York</option>
                                                    <option value="los_angeles">Los Angeles</option>
                                                    <option value="chicago">Chicago</option>
                                                </select></div>
                                            <div class="form-group">
                                                <p class="price-range-title">Price Range</p>
                                                <div class="price_slider_wrapper">
                                                    <div class="price_slider"></div>
                                                    <div class="price_label"><span class="from">$125000</span>-<span
                                                            class="to">$825,000.00</span></div>
                                                </div>
                                            </div>
                                            <div class="advance-btn-wrapper">
                                                <div class="advance-search-btn">
                                                    <div class="search-btn-item"><a class="th-btn radius w-100"><i
                                                                class="fa-regular fa-magnifying-glass me-2"></i> Search
                                                            Property</a></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="sell" role="tabpanel"
                                        aria-labelledby="sell-tab">
                                        <div class="select-group-wrapper">
                                            <div class="form-group"><label for="property_type1">Property Type</label>
                                                <select name="property_type" id="property_type1"
                                                    class="form-select nice-select">
                                                    <option value="" disabled="disabled" selected="selected"
                                                        hidden>
                                                        Select Property Type</option>
                                                    <option value="apartment">Apartment</option>
                                                    <option value="house">House</option>
                                                    <option value="villa">Villa</option>
                                                    <option value="commercial">Commercial</option>
                                                    <option value="land">Land</option>
                                                </select>
                                            </div>
                                            <div class="form-group"><label for="room_type1">Room Type</label> <select
                                                    name="room_type1" id="room_type1" class="form-select nice-select">
                                                    <option value="" disabled="disabled" selected="selected"
                                                        hidden>
                                                        Select Room Type</option>
                                                    <option value="single">Single Room</option>
                                                    <option value="double">Double Room</option>
                                                    <option value="suite">Suite</option>
                                                    <option value="studio">Studio</option>
                                                </select></div>
                                            <div class="form-group"><label for="mini_area1">Mini Area (Sqft)</label>
                                                <select name="mini_area1" id="mini_area1"
                                                    class="form-select nice-select">
                                                    <option value="" disabled="disabled" selected="selected"
                                                        hidden>
                                                        Select Area</option>
                                                    <option value="500">500 Sqft</option>
                                                    <option value="1000">1000 Sqft</option>
                                                    <option value="1500">1500 Sqft</option>
                                                    <option value="2000">2000 Sqft</option>
                                                    <option value="2500">2500+ Sqft</option>
                                                </select>
                                            </div>
                                            <div class="form-group"><label for="max_area1">Max Area (Sqft)</label>
                                                <select name="max_area1" id="max_area1" class="form-select nice-select">
                                                    <option value="" disabled="disabled" selected="selected"
                                                        hidden>
                                                        Select Max Area</option>
                                                    <option value="2000">2000 Sqft</option>
                                                    <option value="3000">3000 Sqft</option>
                                                    <option value="4000">4000 Sqft</option>
                                                    <option value="5000">5000+ Sqft</option>
                                                </select>
                                            </div>
                                            <div class="advance-btn-wrapper">
                                                <div class="advance-search-btn advance-search-active">
                                                    <div class="search-btn-item"><a
                                                            class="th-btn radius outline w-100">Advanced Search <i
                                                                class="fa-solid fa-sliders"></i></a></div>
                                                </div>
                                            </div>
                                            <div class="advance-search-wrapper">
                                                <div class="form-group"><label for="Amenities1">Amenities</label>
                                                    <select name="Amenities1" id="Amenities1"
                                                        class="form-select nice-select">
                                                        <option value="1" disabled="disabled" selected="selected"
                                                            hidden>Select Amenities</option>
                                                        <option value="2">Gymnasium</option>
                                                        <option value="3">Swiming Pool</option>
                                                    </select>
                                                </div>
                                                <div class="form-group"><label for="Garage1">Garage</label> <select
                                                        name="Garage1" id="Garage1" class="form-select nice-select">
                                                        <option value="" disabled="disabled" selected="selected"
                                                            hidden>
                                                            Select Garage</option>
                                                        <option value="1">Small Garage</option>
                                                        <option value="2">Large Garage</option>
                                                        <option value="3">Extra Large Garage</option>
                                                    </select></div>
                                                <div class="form-group"><label for="Floor_Level1">Floor Level</label>
                                                    <select name="Floor_Level1" id="Floor_Level1"
                                                        class="form-select nice-select">
                                                        <option value="Floor_Level" disabled="disabled"
                                                            selected="selected" hidden>Select Floor Level</option>
                                                        <option value="1">Ground Floor</option>
                                                        <option value="2">Middle Floor</option>
                                                        <option value="3">Top Floor</option>
                                                    </select>
                                                </div>
                                                <div class="form-group"><label for="parking_spaces1">Parking
                                                        Spaces</label> <select name="parking_spaces1" id="parking_spaces1"
                                                        class="form-select nice-select">
                                                        <option value="" disabled="disabled" selected="selected"
                                                            hidden>
                                                            Select Parking Spaces</option>
                                                        <option value="1">No Parking</option>
                                                        <option value="2">2 Space</option>
                                                        <option value="3">3 Spaces</option>
                                                    </select></div>
                                            </div>
                                            <div class="form-group"><label for="max_bedrooms11">Max of Bedrooms</label>
                                                <select name="max_bedrooms1" id="max_bedrooms11"
                                                    class="form-select nice-select">
                                                    <option value="" disabled="disabled" selected="selected"
                                                        hidden>
                                                        Select Max Bedrooms</option>
                                                    <option value="1">1 Bedroom</option>
                                                    <option value="2">2 Bedrooms</option>
                                                    <option value="3">3 Bedrooms</option>
                                                    <option value="4">4 Bedrooms</option>
                                                    <option value="5">5+ Bedrooms</option>
                                                </select>
                                            </div>
                                            <div class="form-group"><label for="max_bedrooms12">Max of Bedrooms</label>
                                                <select name="max_bedrooms" id="max_bedrooms12"
                                                    class="form-select nice-select">
                                                    <option value="" disabled="disabled" selected="selected"
                                                        hidden>
                                                        Select Max Bedrooms</option>
                                                    <option value="1">1 Bedroom</option>
                                                    <option value="2">2 Bedrooms</option>
                                                    <option value="3">3 Bedrooms</option>
                                                    <option value="4">4 Bedrooms</option>
                                                    <option value="5">5+ Bedrooms</option>
                                                </select>
                                            </div>
                                            <div class="form-group"><label for="location1">Location</label> <select
                                                    name="location1" id="location1" class="form-select nice-select">
                                                    <option value="" disabled="disabled" selected="selected"
                                                        hidden>
                                                        Select Location</option>
                                                    <option value="new_york">New York</option>
                                                    <option value="los_angeles">Los Angeles</option>
                                                    <option value="chicago">Chicago</option>
                                                    <option value="houston">Houston</option>
                                                    <option value="miami">Miami</option>
                                                </select></div>
                                            <div class="form-group">
                                                <p class="price-range-title">Price Range</p>
                                                <div class="price_slider_wrapper">
                                                    <div class="price_slider"></div>
                                                    <div class="price_label"><span class="from">$125000</span>-<span
                                                            class="to">$825,000.00</span></div>
                                                </div>
                                            </div>
                                            <div class="advance-btn-wrapper">
                                                <div class="advance-search-btn">
                                                    <div class="search-btn-item"><a class="th-btn radius w-100"><i
                                                                class="fa-regular fa-magnifying-glass me-2"></i> Search
                                                            Property</a></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="rent" role="tabpanel"
                                        aria-labelledby="rent-tab">
                                        <div class="select-group-wrapper">
                                            <div class="form-group"><label for="property_type2">Property Type</label>
                                                <select name="property_type2" id="property_type2"
                                                    class="form-select nice-select">
                                                    <option value="" disabled="disabled" selected="selected"
                                                        hidden>
                                                        Select Property Type</option>
                                                    <option value="apartment">Apartment</option>
                                                    <option value="house">House</option>
                                                    <option value="villa">Villa</option>
                                                    <option value="commercial">Commercial</option>
                                                    <option value="land">Land</option>
                                                </select>
                                            </div>
                                            <div class="form-group"><label for="room_type2">Room Type</label> <select
                                                    name="room_type2" id="room_type2" class="form-select nice-select">
                                                    <option value="" disabled="disabled" selected="selected"
                                                        hidden>
                                                        Select Room Type</option>
                                                    <option value="single">Single Room</option>
                                                    <option value="double">Double Room</option>
                                                    <option value="suite">Suite</option>
                                                    <option value="studio">Studio</option>
                                                </select></div>
                                            <div class="form-group"><label for="mini_area2">Mini Area (Sqft)</label>
                                                <select name="mini_area2" id="mini_area2"
                                                    class="form-select nice-select">
                                                    <option value="" disabled="disabled" selected="selected"
                                                        hidden>
                                                        Select Area</option>
                                                    <option value="500">500 Sqft</option>
                                                    <option value="1000">1000 Sqft</option>
                                                    <option value="1500">1500 Sqft</option>
                                                    <option value="2000">2000 Sqft</option>
                                                    <option value="2500">2500+ Sqft</option>
                                                </select>
                                            </div>
                                            <div class="form-group"><label for="max_area2">Max Area (Sqft)</label>
                                                <select name="max_area2" id="max_area2" class="form-select nice-select">
                                                    <option value="" disabled="disabled" selected="selected"
                                                        hidden>
                                                        Select Max Area</option>
                                                    <option value="2000">2000 Sqft</option>
                                                    <option value="3000">3000 Sqft</option>
                                                    <option value="4000">4000 Sqft</option>
                                                    <option value="5000">5000+ Sqft</option>
                                                </select>
                                            </div>
                                            <div class="advance-btn-wrapper">
                                                <div class="advance-search-btn advance-search-active">
                                                    <div class="search-btn-item"><a
                                                            class="th-btn radius outline w-100">Advanced Search <i
                                                                class="fa-solid fa-sliders"></i></a></div>
                                                </div>
                                            </div>
                                            <div class="advance-search-wrapper">
                                                <div class="form-group"><label for="Amenities2">Amenities</label>
                                                    <select name="Amenities2" id="Amenities2"
                                                        class="form-select nice-select">
                                                        <option value="1" disabled="disabled" selected="selected"
                                                            hidden>Select Amenities</option>
                                                        <option value="2">Gymnasium</option>
                                                        <option value="3">Swiming Pool</option>
                                                    </select>
                                                </div>
                                                <div class="form-group"><label for="Garage2">Garage</label> <select
                                                        name="Garage2" id="Garage2" class="form-select nice-select">
                                                        <option value="" disabled="disabled" selected="selected"
                                                            hidden>
                                                            Select Garage</option>
                                                        <option value="1">Small Garage</option>
                                                        <option value="2">Large Garage</option>
                                                        <option value="3">Extra Large Garage</option>
                                                    </select></div>
                                                <div class="form-group"><label for="Floor_Level2">Floor Level</label>
                                                    <select name="Floor_Level2" id="Floor_Level2"
                                                        class="form-select nice-select">
                                                        <option value="Floor_Level" disabled="disabled"
                                                            selected="selected" hidden>Select Floor Level</option>
                                                        <option value="1">Ground Floor</option>
                                                        <option value="2">Middle Floor</option>
                                                        <option value="3">Top Floor</option>
                                                    </select>
                                                </div>
                                                <div class="form-group"><label for="parking_spaces2">Parking
                                                        Spaces</label> <select name="parking_spaces2" id="parking_spaces2"
                                                        class="form-select nice-select">
                                                        <option value="" disabled="disabled" selected="selected"
                                                            hidden>
                                                            Select Parking Spaces</option>
                                                        <option value="1">No Parking</option>
                                                        <option value="2">2 Space</option>
                                                        <option value="3">3 Spaces</option>
                                                    </select></div>
                                            </div>
                                            <div class="form-group"><label for="max_bedrooms22">Max of Bedrooms</label>
                                                <select name="max_bedrooms2" id="max_bedrooms22"
                                                    class="form-select nice-select">
                                                    <option value="" disabled="disabled" selected="selected"
                                                        hidden>
                                                        Select Max Bedrooms</option>
                                                    <option value="1">1 Bedroom</option>
                                                    <option value="2">2 Bedrooms</option>
                                                    <option value="3">3 Bedrooms</option>
                                                    <option value="4">4 Bedrooms</option>
                                                    <option value="5">5+ Bedrooms</option>
                                                </select>
                                            </div>
                                            <div class="form-group"><label for="max_bedrooms2">Max of Bedrooms</label>
                                                <select name="max_bedrooms2" id="max_bedrooms2"
                                                    class="form-select nice-select">
                                                    <option value="" disabled="disabled" selected="selected"
                                                        hidden>
                                                        Select Max Bedrooms</option>
                                                    <option value="1">1 Bedroom</option>
                                                    <option value="2">2 Bedrooms</option>
                                                    <option value="3">3 Bedrooms</option>
                                                    <option value="4">4 Bedrooms</option>
                                                    <option value="5">5+ Bedrooms</option>
                                                </select>
                                            </div>
                                            <div class="form-group"><label for="location2">Location</label> <select
                                                    name="location2" id="location2" class="form-select nice-select">
                                                    <option value="" disabled="disabled" selected="selected"
                                                        hidden>
                                                        Select Location</option>
                                                    <option value="new_york">New York</option>
                                                    <option value="los_angeles">Los Angeles</option>
                                                    <option value="chicago">Chicago</option>
                                                    <option value="houston">Houston</option>
                                                    <option value="miami">Miami</option>
                                                </select></div>
                                            <div class="form-group">
                                                <p class="price-range-title">Price Range</p>
                                                <div class="price_slider_wrapper">
                                                    <div class="price_slider"></div>
                                                    <div class="price_label"><span class="from">$125000</span>-<span
                                                            class="to">$825,000.00</span></div>
                                                </div>
                                            </div>
                                            <div class="advance-btn-wrapper">
                                                <div class="advance-search-btn">
                                                    <div class="search-btn-item"><a class="th-btn radius w-100"><i
                                                                class="fa-regular fa-magnifying-glass me-2"></i> Search
                                                            Property</a></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    {{-- <form method="GET" action="{{ route('properties.index') }}" class="container property-search-form mb-4">
        <div class="row g-3">
            <div class="col-lg-3"><input class="form-control" name="q" value="{{ request('q') }}"
                    placeholder="Search properties"></div>
            <div class="col-lg-2"><input class="form-control" name="city" value="{{ request('city') }}"
                    placeholder="City"></div>
            <div class="col-lg-2"><select class="form-select" name="listing_type">
                    <option value="">Buy or rent</option>
                    <option value="sale" @selected(request('listing_type') === 'sale')>For sale</option>
                    <option value="rent" @selected(request('listing_type') === 'rent')>For rent</option>
                    <option value="lease" @selected(request('listing_type') === 'lease')>For lease</option>
                </select></div>
            <div class="col-lg-2"><input class="form-control" type="number" name="min_price"
                    value="{{ request('min_price') }}" placeholder="Min price"></div>
            <div class="col-lg-2"><input class="form-control" type="number" name="max_price"
                    value="{{ request('max_price') }}" placeholder="Max price"></div>
            <div class="col-lg-1"><button class="th-btn w-100">Search</button></div>
        </div>
    </form> --}}
    <section class="th-blog-wrapper space-top space-extra-bottom">
        <div class="container">
            <div class="th-sort-bar property-style">
                <div class="row justify-content-between align-items-center">
                    <div class="col-md">
                        <h4 class="box-title text-start fadeinup wow" data-wow-duration="1.5s" data-wow-delay="0.1s">
                            Property Listing</h4>
                    </div>
                    <div class="col-md-auto">
                        <div class="sorting-filter-wrap fadeinup wow" data-wow-duration="1.5s" data-wow-delay="0.3s">
                            <div class="nav" role="tablist"><a class="active" href="property.html#"
                                    id="tab-shop-list" data-bs-toggle="tab" data-bs-target="#tab-list" role="tab"
                                    aria-controls="tab-grid" aria-selected="false"><i class="fa-solid fa-list"></i></a>
                                <a href="property.html#" id="tab-shop-grid" data-bs-toggle="tab"
                                    data-bs-target="#tab-grid" role="tab" aria-controls="tab-grid"
                                    aria-selected="true"><i class="fa-light fa-grid-2"></i></a>
                            </div>
                            <form class="woocommerce-ordering" method="get"><select name="orderby" class="orderby"
                                    aria-label="Shop order" onchange="this.form.submit()">
                                    @foreach (['menu_order' => 'Default Sorting', 'popularity' => 'Featured first', 'date' => 'Sort by latest', 'price' => 'Sort by price: low to high', 'price-desc' => 'Sort by price: high to low'] as $sortValue => $sortLabel)
                                        <option value="{{ $sortValue }}" @selected(request('orderby', 'menu_order') === $sortValue)>
                                            {{ $sortLabel }}
                                        </option>
                                    @endforeach
                                </select>
                                @foreach (request()->except(['orderby', 'page']) as $key => $value)
                                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                @endforeach
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-content" id="nav-tabContent">
                <div class="tab-pane fade" id="tab-grid" role="tabpanel" aria-labelledby="tab-shop-grid">
                    <div class="row gy-40">
                        @forelse ($properties as $property)
                            <div class="col-xl-4 col-lg-6 col-md-6 fadeinup wow">
                                <div class="popular-list-1 grid-style">@php
                                    $listingImages = collect();
                                    if ($property->featured_image) {
                                        $listingImages->push(
                                            (object) [
                                                'path' => $property->featured_image,
                                                'alt_text' => $property->title,
                                            ],
                                        );
                                    }
                                    foreach ($property->images as $listingImage) {
                                        if ($listingImage->path !== $property->featured_image) {
                                            $listingImages->push($listingImage);
                                        }
                                    }
                                @endphp
                                    <div class="thumb-wrapper">
                                        <div class="th-slider"
                                            data-slider-options='{"loop":false, "autoplay": false,"autoHeight": true, "effect":"fade"}'>
                                            <div class="swiper-wrapper">
                                                @forelse($listingImages as $listingImage)
                                                    <div class="swiper-slide"><a class="popular-popup-image"
                                                            href="{{ Storage::disk('public')->url($listingImage->path) }}"><img
                                                                src="{{ Storage::disk('public')->url($listingImage->path) }}"
                                                                alt="{{ $listingImage->alt_text ?: $property->title }}"></a>
                                                    </div>
                                                @empty
                                                    <div class="swiper-slide">
                                                        <div
                                                            class="property-image-placeholder bg-smoke d-flex align-items-center justify-content-center">
                                                            <span class="text-muted">Image pending</span>
                                                        </div>
                                                    </div>
                                                @endforelse
                                            </div>
                                            <div class="icon-wrap"><button class="slider-arrow slider-prev"><i
                                                        class="far fa-arrow-left"></i></button> <button
                                                    class="slider-arrow slider-next"><i
                                                        class="far fa-arrow-right"></i></button></div>
                                        </div>
                                        <div class="actions"><a href="#" class="icon-btn"><i
                                                    class="fas fa-heart"></i></a></div>
                                        <div class="actions-style-2-wrapper">
                                            <div class="actions style-2"><a href="property.html#" class="icon-btn"><span
                                                        class="action-text">Add To Favorite</span> <i
                                                        class="fa-solid fa-bookmark"></i> </a><a
                                                    href="{{ $listingImages->isNotEmpty() ? Storage::disk('public')->url($listingImages->first()->path) : route('properties.show', $property) }}"
                                                    class="icon-btn {{ $listingImages->isNotEmpty() ? 'popular-popup-image' : '' }}"><span
                                                        class="action-text">View all img</span> <i
                                                        class="fa-solid fa-camera"></i></a></div>
                                        </div>
                                        <div class="popular-badge"><img
                                                src="{{ asset('theme-assets/img/icon/sell_rent_icon.svg') }}"
                                                alt="icon">
                                            <p>{{ $property->listing_type === 'rent' ? 'For Rent' : ($property->listing_type === 'lease' ? 'For Lease' : 'For Sale') }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="property-content">
                                        <div class="media-body">
                                            <h3 class="box-title"><a
                                                    href="{{ route('properties.show', $property) }}">{{ $property->title }}</a>
                                            </h3>
                                            <div class="box-text">
                                                <div class="icon"><img
                                                        src="{{ asset('theme-assets/img/icon/popular-location.svg') }}"
                                                        alt="icon"></div>
                                                {{ trim(($property->address ? $property->address . ', ' : '') . $property->city) }}
                                            </div>
                                        </div>
                                        <ul class="property-featured">
                                            <li>
                                                <div class="icon"><img
                                                        src="{{ asset('theme-assets/img/icon/bed.svg') }}"
                                                        alt="icon"></div>Bed {{ $property->bedrooms ?? '—' }}
                                            </li>
                                            <li>
                                                <div class="icon"><img
                                                        src="{{ asset('theme-assets/img/icon/bath.svg') }}"
                                                        alt="icon"></div>Bath {{ $property->bathrooms ?? '—' }}
                                            </li>
                                            <li>
                                                <div class="icon"><img
                                                        src="{{ asset('theme-assets/img/icon/sqft.svg') }}"
                                                        alt="icon"></div>
                                                {{ $property->area ? number_format((float) $property->area) . ' ' . ($property->area_unit ?? 'sqft') : '—' }}
                                            </li>
                                        </ul>
                                        <div class="property-bottom">
                                            <h6 class="box-title price-red-shine">
                                                {{ $property->price ? '₹ ' . number_format((float) $property->price, 2) : $property->price_label ?? 'Price on request' }}
                                            </h6><a class="th-btn sm style3 pill"
                                                href="{{ route('properties.show', $property) }}">View More</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center text-muted">No matching properties are available.</div>
                        @endforelse
                    </div>
                </div>
                <div class="tab-pane fade active show" id="tab-list" role="tabpanel" aria-labelledby="tab-shop-list">
                    <div class="row gy-40 justify-content-center">
                        <div class="col-xl-8 col-lg-12">
                            <div class="row gy-30">
                                @forelse ($properties as $property)
                                    <div class="col-xl-12 fadeinup wow">
                                        <div class="popular-list-1 list-style">@php
                                            $listingImages = collect();
                                            if ($property->featured_image) {
                                                $listingImages->push(
                                                    (object) [
                                                        'path' => $property->featured_image,
                                                        'alt_text' => $property->title,
                                                    ],
                                                );
                                            }
                                            foreach ($property->images as $listingImage) {
                                                if ($listingImage->path !== $property->featured_image) {
                                                    $listingImages->push($listingImage);
                                                }
                                            }
                                        @endphp
                                            <div class="thumb-wrapper">
                                                <div class="th-slider"
                                                    data-slider-options='{"loop":false, "autoplay": false,"autoHeight": true, "effect":"fade"}'>
                                                    <div class="swiper-wrapper">
                                                        @forelse($listingImages as $listingImage)
                                                            <div class="swiper-slide"><a class="popular-popup-image"
                                                                    href="{{ Storage::disk('public')->url($listingImage->path) }}"><img
                                                                        src="{{ Storage::disk('public')->url($listingImage->path) }}"
                                                                        alt="{{ $listingImage->alt_text ?: $property->title }}"></a>
                                                            </div>
                                                        @empty
                                                            <div class="swiper-slide">
                                                                <div
                                                                    class="property-image-placeholder bg-smoke d-flex align-items-center justify-content-center">
                                                                    <span class="text-muted">Image pending</span>
                                                                </div>
                                                            </div>
                                                        @endforelse
                                                    </div>
                                                    <div class="icon-wrap"><button class="slider-arrow slider-prev"><i
                                                                class="far fa-arrow-left"></i></button> <button
                                                            class="slider-arrow slider-next"><i
                                                                class="far fa-arrow-right"></i></button></div>
                                                </div>
                                                <div class="actions"><a href="#" class="icon-btn"><i
                                                            class="fas fa-heart"></i></a></div>
                                                <div class="actions-style-2-wrapper">
                                                    <div class="actions style-2"><a href="property.html#"
                                                            class="icon-btn"><span class="action-text">Add To
                                                                Favorite</span> <i class="fa-solid fa-bookmark"></i> </a><a
                                                            href="{{ $listingImages->isNotEmpty() ? Storage::disk('public')->url($listingImages->first()->path) : route('properties.show', $property) }}"
                                                            class="icon-btn {{ $listingImages->isNotEmpty() ? 'popular-popup-image' : '' }}"><span
                                                                class="action-text">View all img</span> <i
                                                                class="fa-solid fa-camera"></i></a></div>
                                                </div>
                                                <div class="popular-badge"><img
                                                        src="{{ asset('theme-assets/img/icon/sell_rent_icon.svg') }}"
                                                        alt="icon">
                                                    <p>{{ $property->listing_type === 'rent' ? 'For Rent' : ($property->listing_type === 'lease' ? 'For Lease' : 'For Sale') }}
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="property-content">
                                                <div class="media-body">
                                                    <h3 class="box-title"><a
                                                            href="{{ route('properties.show', $property) }}">{{ $property->title }}</a>
                                                    </h3>
                                                    <div class="box-text">
                                                        <div class="icon"><img
                                                                src="{{ asset('theme-assets/img/icon/popular-location.svg') }}"
                                                                alt="icon"></div>
                                                        {{ trim(($property->address ? $property->address . ', ' : '') . $property->city) }}
                                                    </div>
                                                </div>
                                                <ul class="property-featured">
                                                    <li>
                                                        <div class="icon"><img
                                                                src="{{ asset('theme-assets/img/icon/bed.svg') }}"
                                                                alt="icon"></div>Bed {{ $property->bedrooms ?? '—' }}
                                                    </li>
                                                    <li>
                                                        <div class="icon"><img
                                                                src="{{ asset('theme-assets/img/icon/bath.svg') }}"
                                                                alt="icon"></div>Bath
                                                        {{ $property->bathrooms ?? '—' }}
                                                    </li>
                                                    <li>
                                                        <div class="icon"><img
                                                                src="{{ asset('theme-assets/img/icon/sqft.svg') }}"
                                                                alt="icon"></div>
                                                        {{ $property->area ? number_format((float) $property->area) . ' ' . ($property->area_unit ?? 'sqft') : '—' }}
                                                    </li>
                                                </ul>
                                                <div class="property-bottom">
                                                    <h6 class="box-title price-red-shine">
                                                        {{ $property->price ? '₹ ' . number_format((float) $property->price, 2) : $property->price_label ?? 'Price on request' }}
                                                    </h6><a class="th-btn sm style3 pill"
                                                        href="{{ route('properties.show', $property) }}">View More</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12 text-center text-muted">No matching properties are available.</div>
                                @endforelse
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-6">
                            <aside class="sidebar-area">
                                <div class="widget widget_search">
                                    <h3 class="widget_title">search</h3>
                                    <form class="search-form" method="GET" action="{{ route('properties.index') }}">
                                        <input type="search" name="q" value="{{ request('q') }}"
                                            placeholder="Enter Keyword"> <button type="submit"><i
                                                class="far fa-search"></i></button>
                                    </form>
                                </div>
                                <div class="widget">
                                    <h3 class="widget_title">Featured Properties</h3>
                                    <div class="recent-post-wrap featured-listing">
                                        @forelse($featuredProperties as $featuredProperty)
                                            <div class="recent-post">
                                                <div class="media-img"><a
                                                        href="{{ route('properties.show', $featuredProperty) }}">
                                                        @if ($featuredProperty->featured_image)
                                                            <img src="{{ Storage::disk('public')->url($featuredProperty->featured_image) }}"
                                                                alt="{{ $featuredProperty->title }}">
                                                        @endif
                                                    </a>
                                                </div>
                                                <div class="media-body">
                                                    <h4 class="post-title"><a class="text-inherit"
                                                            href="{{ route('properties.show', $featuredProperty) }}">{{ $featuredProperty->title }}</a>
                                                    </h4>
                                                    <div class="property-features-wrap">
                                                        <div class="property-features-item">
                                                            <div class="thumb"><img
                                                                    src="{{ asset('theme-assets/img/icon/bed.svg') }}"
                                                                    alt="Bedrooms"></div>
                                                            <h5 class="feature-title">Bed
                                                                {{ $featuredProperty->bedrooms ?? '—' }}</h5>
                                                        </div>
                                                        <div class="divider"></div>
                                                        <div class="property-features-item">
                                                            <div class="thumb"><img
                                                                    src="{{ asset('theme-assets/img/icon/bath.svg') }}"
                                                                    alt="Bathrooms"></div>
                                                            <h5 class="feature-title">Bath
                                                                {{ $featuredProperty->bathrooms ?? '—' }}</h5>
                                                        </div>
                                                        <div class="divider"></div>
                                                        <div class="property-features-item">
                                                            <div class="thumb"><img
                                                                    src="{{ asset('theme-assets/img/icon/sqft.svg') }}"
                                                                    alt="Area"></div>
                                                            <h5 class="feature-title">
                                                                {{ $featuredProperty->area ? number_format((float) $featuredProperty->area) . ' ' . ($featuredProperty->area_unit ?? 'sqft') : '— sqft' }}
                                                            </h5>
                                                        </div>
                                                    </div>
                                                    <div class="recent-post-meta price-red-shine">
                                                        {{ $featuredProperty->price ? '₹ ' . number_format((float) $featuredProperty->price, 2) : $featuredProperty->price_label ?? 'Price on request' }}
                                                    </div>
                                                </div>
                                        </div>@empty<p class="text-muted">No featured properties are currently
                                                available.</p>
                                        @endforelse
                                    </div>
                                </div>
                                <div class="widget sidebar-contact-form">
                                    <h3 class="widget_title">Contact Us</h3>
                                    <div class="contact-form-widget">
                                        <form action="{{ route('enquiries.store') }}" method="POST"
                                            class="newsletter-form">
                                            @csrf
                                            <input type="hidden" name="subject" value="Property enquiry">
                                            <div class="form-group"><input class="form-control" type="text"
                                                    name="name" value="{{ old('name') }}" placeholder="Your Name"
                                                    required maxlength="150"></div>
                                            <div class="form-group"><input class="form-control" type="email"
                                                    name="email" value="{{ old('email') }}" placeholder="Your Email"
                                                    required></div>
                                            <div class="form-group"><input class="form-control" type="text"
                                                    name="phone" value="{{ old('phone') }}" placeholder="Your Phone"
                                                    required maxlength="30"></div>
                                            <div class="form-group mb-4">
                                                <textarea name="message" id="property-list-enquiry-message" cols="30" rows="3" class="form-control"
                                                    placeholder="Your Message">{{ old('message') }}</textarea>
                                            </div>
                                            <div class="form-btn pt-2"><button type="submit" class="th-btn radius">Send
                                                    Us</button></div>
                                        </form>
                                    </div>
                                </div>
                                <div class="widget widget_banner"
                                    data-bg-src="{{ asset('theme-assets/img/bg/widget_banner.jpg') }}">
                                    <div class="widget-banner">
                                        <h2 class="title">We can help you to find real estate agency</h2><a
                                            href="{{ route('contact') }}" class="th-btn radius">Contact with Agent</a>
                                    </div>
                                </div>
                            </aside>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div class="container mt-4">{{ $properties->withQueryString()->links('vendor.pagination.buraaqh') }}</div>
@endsection
