<form wire:submit="save">
    <div class="tp-newsletter-input-box p-relative">
        <input type="email" placeholder="Entrer votre Email" wire:model="email">
        <button class="tp-btn-theme" type="@if ($end) button @else submit @endif">
            @if ($end)
                Enegistré !
            @else
                <span wire:loading>
                    <img src="/icons/kOnzy.gif" height="20" width="20" alt="" srcset="">
                </span>
                <span>M'abonner</span>
            @endif
        </button>
    </div>
</form>
