@extends('superadmin.layout')

@section('title', 'Pricing Management')
@section('page_title', 'Pricing Components Management')

@section('css')
    <style>
        .pricing-table {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .pricing-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .pricing-table table {
            margin-bottom: 0;
        }

        .pricing-table thead {
            background: #f8fafc;
        }

        .pricing-table th {
            border: none;
            padding: 15px;
            font-weight: 600;
            color: #64748b;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .pricing-table td {
            border: none;
            padding: 15px;
            color: #475569;
        }

        .pricing-key {
            font-family: 'Courier New', monospace;
            background: #f1f5f9;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            color: #0f172a;
        }

        .price-value {
            font-size: 16px;
            font-weight: 700;
            color: #7c3aed;
        }

        .btn-edit {
            background: #3b82f6;
            color: white;
            text-decoration: none;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .btn-edit:hover {
            background: #2563eb;
            color: white;
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
        }

        .empty-state i {
            font-size: 48px;
            color: #cbd5e1;
            display: block;
            margin-bottom: 15px;
        }
    </style>
@endsection

@section('content')
    <div class="pricing-header">
        <h5 class="mb-0"><i class="fas fa-tags me-2"></i>All Pricing Components</h5>
        <small class="text-muted">Total: {{ $pricing->total() }} components</small>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="pricing-table">
        @if ($pricing->isEmpty())
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <p class="text-muted">No pricing components found</p>
            </div>
        @else
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Key</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Description</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pricing as $component)
                        <tr>
                            <td>
                                <span class="pricing-key">{{ $component->key }}</span>
                            </td>
                            <td>
                                <strong>{{ $component->name }}</strong>
                            </td>
                            <td>
                                <span class="price-value">Rp {{ number_format($component->price, 0, ',', '.') }}</span>
                            </td>
                            <td>
                                <small class="text-muted">{{ $component->description }}</small>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('superadmin.pricing.edit', $component) }}" class="btn-edit">
                                    <i class="fas fa-pen"></i>Edit
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    @if ($pricing->hasPages())
        <div class="mt-4">
            {{ $pricing->links() }}
        </div>
    @endif

    <div class="mt-4">
        <div class="card bg-light border-0">
            <div class="card-body">
                <h6 class="mb-2"><i class="fas fa-info-circle me-2"></i>Info</h6>
                <small class="text-muted">
                    These pricing components are used calculating subscription costs. Changes will be reflected
                    in new subscription calculations.
                </small>
            </div>
        </div>
    </div>
@endsection
