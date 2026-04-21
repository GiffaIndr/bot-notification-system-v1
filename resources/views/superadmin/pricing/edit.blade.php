@extends('superadmin.layout')

@section('title', 'Edit Pricing - ' . $pricing->name)
@section('page_title', 'Edit Pricing Component')

@section('css')
    <style>
        .form-card {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            max-width: 600px;
        }

        .form-section {
            margin-bottom: 25px;
        }

        .form-section:last-child {
            margin-bottom: 0;
        }

        .section-title {
            font-size: 14px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 15px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            font-weight: 600;
            color: #1a202c;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .form-control {
            padding: 10px 15px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
        }

        .form-control:focus {
            border-color: #7c3aed;
            box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
        }

        .key-badge {
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            padding: 10px 15px;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            color: #0f172a;
            font-size: 13px;
        }

        .price-info {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 12px;
            border-radius: 4px;
            font-size: 13px;
            color: #92400e;
            margin-top: 10px;
        }

        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 30px;
        }

        .btn-submit {
            background: #7c3aed;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            flex: 1;
        }

        .btn-submit:hover {
            background: #6d28d9;
        }

        .btn-cancel {
            background: #f1f5f9;
            color: #64748b;
            border: 1px solid #e2e8f0;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
        }

        .btn-cancel:hover {
            border-color: #cbd5e1;
            color: #1a202c;
        }
    </style>
@endsection

@section('content')
    <div class="form-card">
        <h5 class="mb-4"><i class="fas fa-pen me-2"></i>Edit Pricing Component</h5>

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Validation Failed!</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form action="{{ route('superadmin.pricing.update', $pricing) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-section">
                <div class="section-title">Component Information</div>

                <div class="form-group">
                    <label>Key (Read-only)</label>
                    <div class="key-badge">{{ $pricing->key }}</div>
                </div>

                <div class="form-group">
                    <label for="name">Name *</label>
                    <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name', $pricing->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-section">
                <div class="section-title">Pricing</div>

                <div class="form-group">
                    <label for="price">Price (Rp) *</label>
                    <input type="number" id="price" name="price" class="form-control @error('price') is-invalid @enderror"
                           value="{{ old('price', $pricing->price) }}" min="0" step="1" required>
                    @error('price')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="price-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Current price: <strong>Rp {{ number_format($pricing->price, 0, ',', '.') }}</strong>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <div class="section-title">Description</div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror"
                              rows="3">{{ old('description', $pricing->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="button-group">
                <button type="submit" class="btn-submit">
                    <i class="fas fa-save me-2"></i>Save Changes
                </button>
                <a href="{{ route('superadmin.pricing.index') }}" class="btn-cancel">
                    <i class="fas fa-times"></i>Cancel
                </a>
            </div>
        </form>
    </div>
@endsection
