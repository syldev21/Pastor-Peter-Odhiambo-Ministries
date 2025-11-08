<form method="POST" action="{{ route('checkout.store') }}" class="space-y-4">
    @csrf

    <h2 class="text-xl font-bold">Delivery Details</h2>

    <input type="text" name="name" placeholder="Full Name" required class="w-full border p-2 rounded">
    <input type="text" name="phone" placeholder="Phone Number" required class="w-full border p-2 rounded">
    <input type="text" name="address" placeholder="Delivery Address" required class="w-full border p-2 rounded">

    <h2 class="text-xl font-bold mt-6">Order Summary</h2>

    @foreach($cartItems as $id => $item)
        <div class="border p-2 rounded">
            {{ auth()->check() ? $item->book->title : $item['title'] }} —
            Qty: {{ auth()->check() ? $item->quantity : $item['quantity'] }} —
            KES {{ auth()->check() ? $item->book->price * $item->quantity : $item['price'] * $item['quantity'] }}
        </div>
    @endforeach

    <div class="text-right font-bold mt-4">
        Total: KES {{ number_format($total, 2) }}
    </div>

    <button type="submit" class="bg-green-600 text-white px-6 py-3 rounded hover:bg-green-700">
        Confirm Order
    </button>
</form>