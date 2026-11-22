@extends('admin.layouts.app')
@section('title', 'Newsletter subscribers')
@section('content')
    <div class="mb-4">
        <h1 class="page-title">Newsletter subscribers</h1>
        <p class="text-muted">Email subscriptions collected from the website footer and newsletter forms.</p>
    </div>
    <div class="content-card">
        <div class="table-responsive">
            <table class="table align-middle" data-datatable>
                <thead>
                    <tr>
                        <th>Email</th>
                        <th>Subscribed</th>
                        <th>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subscribers as $subscriber)
                        <tr>
                            <td>{{ $subscriber->email }}</td>
                            <td>{{ $subscriber->subscribed_at?->format('d M Y H:i') }}</td>
                            <td class="text-end">
                                <form method="POST" action="{{ route('admin.newsletters.destroy', $subscriber) }}"
                                    onsubmit="return confirm('Remove this subscriber?')">@csrf @method('DELETE')<button
                                        class="btn btn-sm btn-outline-danger">Remove</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted py-4">No subscribers yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>{{ $subscribers->links() }}
    </div>
@endsection
