<div class="header-notifications-list">
    {{-- Affichage du bouton "Tout effacer" uniquement s'il y a des notifications --}}
    @if($notifications->isNotEmpty())
        <div class="dropdown-item d-flex justify-content-end border-bottom border-gray-200 py-2" style="background: #fafafa;">
            <button onclick="confirmerToutEffacerNotifications()" 
                    style="background: none; border: none; color: #dc3545; font-size: 13px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 5px;">
                <i class="ri-delete-bin-line"></i> Tout effacer
            </button>
        </div>
    @endif

    @forelse ($notifications as $notification)
        <div class="dropdown-item">
            <div class="d-flex align-items-center">
                <div class="notify bg-light-primary text-primary">
                    @if ($notification->type == 'commande')
                        <i class="ri-shopping-bag-line text-primary-color"></i>
                    @elseif ($notification->type == 'mesage')
                        <i class="ri-chat-3-line text-primary-color"></i>
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

    {{-- Script JavaScript de confirmation SweetAlert2 --}}
    <script>
        function confirmerToutEffacerNotifications() {
            Swal.fire({
                title: 'Tout effacer ?',
                text: 'Voulez-vous vraiment supprimer toutes vos notifications définitivement ?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Oui, tout supprimer',
                cancelButtonText: 'Annuler',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Remplacez 'deleteAll' par le nom exact de votre méthode dans le composant PHP
                    @this.call('deleteAll'); 
                    
                    Swal.fire({
                        title: 'Supprimé !',
                        text: 'Toutes les notifications ont été effacées.',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            });
        }
    </script>
</div>