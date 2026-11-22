@extends('admin.layouts.app')
@section('title', 'Property inquiries & contact queries')
@section('content')
    <div class="mb-4">
        <h1 class="page-title">Property inquiries & contact queries</h1>
        <p class="text-muted mb-0">Search enquiries, review property leads and manage contact requests.</p>
    </div>
    <div class="content-card mb-4">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-5">
                <label class="form-label">Search</label>
                <input class="form-control" name="q" value="{{ $filters['q'] ?? '' }}"
                    placeholder="Name, email, phone, message or property">
            </div>
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select class="form-select" name="status">
                    <option value="">All statuses</option>
                    @foreach (['new', 'contacted', 'qualified', 'closed', 'spam'] as $status)
                        <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Source</label>
                <select class="form-select" name="source">
                    <option value="">All sources</option>
                    <option value="website" @selected(($filters['source'] ?? '') === 'website')>Website</option>
                    <option value="property" @selected(($filters['source'] ?? '') === 'property')>Property</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button class="btn btn-primary flex-grow-1">Filter</button>
                <a class="btn btn-light" href="{{ route('admin.enquiries.index') }}">Reset</a>
            </div>
        </form>
    </div>
    <div class="content-card">
        <div class="table-responsive">
            <table class="table align-middle" data-datatable>
                <thead>
                    <tr>
                        <th>Contact</th>
                        <th>Property / subject</th>
                        <th>Message</th>
                        <th>Source</th>
                        <th>Status & notes</th>
                        <th>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($enquiries as $e)
                        <tr>
                            <td>
                                <strong>{{ $e->name }}</strong>
                                <div class="small text-muted">{{ $e->phone }}<br>{{ $e->email }}</div>
                                <div class="small text-muted">{{ $e->created_at?->format('d M Y, h:i A') }}</div>
                            </td>
                            <td>{{ $e->property?->title ?? ($e->subject ?: 'General contact query') }}@if ($e->preferred_date)
                                    <div class="small text-muted">{{ $e->preferred_date->format('d M Y') }}
                                        {{ $e->preferred_time }}</div>
                                @endif
                            </td>
                            <td style="max-width:260px;white-space:pre-wrap">{{ $e->message }}</td>
                            <td>
                                <span class="badge text-bg-light">{{ ucfirst($e->source) }}</span>
                            </td>
                            <td>
                                <form method="POST" action="{{ route('admin.enquiries.update', $e) }}">@csrf
                                    @method('PATCH')<select name="status" class="form-select form-select-sm mb-2">
                                        @foreach (['new', 'contacted', 'qualified', 'closed', 'spam'] as $status)
                                            <option value="{{ $status }}" @selected($e->status === $status)>
                                                {{ ucfirst($status) }}</option>
                                        @endforeach
                                    </select>
                                    <textarea class="form-control form-control-sm mb-2" name="admin_notes" rows="2" placeholder="Internal notes">{{ $e->admin_notes }}</textarea>
                                    <button class="btn btn-sm btn-primary">Save</button>
                                </form>
                            </td>
                            <td>
                                <form method="POST" action="{{ route('admin.enquiries.destroy', $e) }}"
                                    onsubmit="return confirm('Delete this enquiry?')">@csrf @method('DELETE')<button
                                        class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @empty<tr>
                                <td colspan="6" class="text-center py-5 text-muted">No enquiries match these filters.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>{{ $enquiries->links() }}
        </div>
    @endsection
