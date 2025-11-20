@props(['order'])

<div class="border rounded-lg p-4 mb-4 shadow-sm bg-white">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-lg font-semibold text-gray-800">Order #{{ $order->checkout_id }}</h2>
            <p class="text-sm text-gray-600">Placed on {{ $order->created_at->format('M d, Y') }}</p>
        </div>
        <div class="text-right">
            <span class="text-sm font-medium text-green-600">
                {{ ucfirst($order->payment_status) }}
            </span>
        </div>
    </div>

    <div class="mt-2 text-sm text-gray-700">
        {{ $order->summary ?? 'No summary available.' }}
    </div>

    <div class="mt-4 text-right">
        <a href="{{ route('orders.show', $order) }}"
           class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
            View Details →
        </a>
    </div>
</div>