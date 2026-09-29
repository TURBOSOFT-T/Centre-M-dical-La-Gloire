<div class="header-notifications-list">
    {{-- Affichage du bouton "Tout effacer" uniquement s'il y a des notifications --}}
    @if($notifications->isNotEmpty())
    <div class="dropdown-item d-flex justify-content-end border-bottom border-gray-200 py-2" style="background: #fafafa;">
        @if($notifications->isNotEmpty())
    <div class="dropdown-item d-flex justify-content-end border-bottom border-gray-200 py-2" style="background: #fafafa;">
        <button type="button" 
                wire:click="deleteAll" 
                 style="background: none; border: none; color: #dc3545; font-size: 13px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 5px;">
            <i class="ri-delete-bin-line"></i> Tout effacer
        </button>


       
    </div>
@endif

        
    </div>
    @endif

    @forelse ($notifications as $notification)
    <div class="dropdown-item">
        <div class="d-flex align-items-center">
            <div class="notify bg-light-primary text-primary">
                @if ($notification->type == 'commande')
                <i class="ri-shopping-bag-line text-primary-color"></i>
                @elseif ($notification->type == 'message' || $notification->type == 'mesage')
                <i class="ri-chat-3-line text-primary-color"></i>
                @elseif ($notification->type == 'consultation_creation')
                <i class="ri-user-add-line text-primary-color"></i>
                @elseif ($notification->type == 'consultation_modification')
                <i class="ri-edit-box-line text-primary-color"></i>
                @elseif ($notification->type == 'stock')
                <i class="ri-stock-line text-primary-color"></i>
                @else
                <i class="bx bx-group text-primary-color"></i>
                @endif
            </div>
            <div class="flex-grow-1" style="cursor: pointer;" onclick="url('{{ $notification->url }}')">
                <h6 class="msg-name">
                    {{ $notification->titre }}
                    <span class="msg-time float-end">
                        {{ $notification->created_at->diffForHumans() }}
                    </span>
                </h6>
                <p class="msg-info">
                    {{ $notification->message }}
                </p>
            </div>
            <div style="cursor: pointer;">
                <i class="ri-close-fill" wire:click="delete({{ $notification->id }})"></i>
            </div>
        </div>
    </div>
    @empty
    <a class="dropdown-item d-flex w-100 py-3 text-muted text-center fw-bold border-bottom border-gray-200">
        Aucune notification en ce moment !
    </a>
    @endforelse

</div>