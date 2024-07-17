<div class="form-group">
    <label for="payment_method">Payment Method <span
            class="text-danger">*</span></label>
    <select class="form-control" id="payment_method" name="payment_method"
        required>
        <option disabled hidden selected>Choose Payment Method</option>
        @foreach ($payment_method as $pm)
            @if (!is_null(old('payment_method')) && old('payment_method') == $pm->id)
                <option value="{{ $pm->id }}" selected>
                    {{ $pm->name }}
                </option>
            @else
                <option value="{{ $pm->id }}">
                    {{ $pm->name }}</option>
            @endif
        @endforeach
    </select>
</div>
