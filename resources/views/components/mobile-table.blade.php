@props(['headers' => [], 'data' => [], 'mobile' => true])

@if($mobile && request()->isMobile())
    <!-- Mobile Card Layout -->
    <div class="list-mobile">
        @foreach($data as $row)
            <div class="list-mobile-item">
                @foreach($headers as $key => $header)
                    <div class="flex justify-between items-center py-2 border-b border-gray-100 last:border-b-0">
                        <span class="text-sm font-medium text-gray-600">{{ $header }}:</span>
                        <span class="text-sm text-gray-900">{{ $row[$key] ?? '' }}</span>
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>
@else
    <!-- Desktop Table Layout -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    @foreach($headers as $header)
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ $header }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($data as $row)
                    <tr>
                        @foreach($headers as $key => $header)
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $row[$key] ?? '' }}
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif

