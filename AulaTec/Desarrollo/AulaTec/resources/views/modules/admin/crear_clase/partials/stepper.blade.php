<div class="flex items-center space-x-3">
    @foreach($steps as $step)
        <div class="flex items-center">
            <div class="w-3.5 h-3.5 rounded-full 
                {{ $step->isCurrent() || $step->isPrevious() ? 'bg-purple-600' : 'bg-gray-300' }}
                {{ $step->isPrevious() ? 'cursor-pointer' : '' }}"
                @if($step->isPrevious())
                    wire:click="{{ $step->show() }}"
                @endif
            ></div>
        </div>
    @endforeach
</div>