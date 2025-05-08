<div class="filter-frame">
    <button id="hamburgerButton" class="hamburger-button-S">Advanced Filters</button>
    <div id="advancedFiltersMenu" class="advanced-filters hidden">
        <h3>Search by attributes:</h3>

        <ul>
            @forelse ($attributes as $attribute)
            <li class="filter-item">
                @php
                    $currentFilter = request()->query('filter');
                    $newUrl = url()->current() . '?tag=' . $tag . '&filter=' . ($currentFilter == $attribute ? '' : $attribute);
                    $isChecked = $currentFilter == $attribute;
                @endphp
                <a href="{{ $newUrl }}">
                    <span class="filter-text">{{ $attribute }}</span>
                    @if($isChecked)
                        <span class="checkmark">&#10003;</span>  
                    @endif
                </a>
            </li>
            @empty
                <li class="filter-item">No attributes available</li>
            @endforelse
        </ul>
    </div>
</div>
