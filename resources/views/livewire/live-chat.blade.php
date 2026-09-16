<div>
    <!-- Icône / Bouton de Tchat Flottant (en bas à droite) -->
    <div class="chat-trigger {{ $ouvert ? 'active' : '' }}" wire:click="toggleChat">
        @if($ouvert)
            <i class="fas fa-times"></i> <!-- Icône de fermeture (X) -->
        @else
            <i class="fas fa-comments"></i> <!-- Icône de tchat standard -->
        @endif
    </div>

    <!-- Fenêtre de Discussion Directe -->
    @if($ouvert)
    <div class="chat-window" wire:poll.1s>
        <!-- En-tête -->
        <div class="chat-header">
            <div class="chat-title">
                <span class="online-indicator"></span>
                {{ \App\Helpers\TranslationHelper::TranslateText('SWOOT BIO DIRECT') }}
            </div>
        </div>

        <!-- Corps de la discussion -->
        <div class="chat-body" id="chat-messages-box">
            @if($messages->isEmpty())
                <div class="chat-empty">
                    <i class="fas fa-comment-dots"></i>
                    <p>{{ \App\Helpers\TranslationHelper::TranslateText('Posez votre question. Un conseiller va vous répondre.') }}</p>
                </div>
            @else
                @foreach($messages as $msg)
                    <div class="chat-bubble-container {{ $msg->expediteur === 'client' ? 'client-side' : 'admin-side' }}">
                        <div class="chat-bubble">
                            {{ $msg->contenu }}
                            <span class="chat-time">{{ $msg->created_at->format('H:i') }}</span>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

      <footer class="chat-footer">
        <!-- Formulaire d'envoi -->
        <form wire:submit.prevent="envoyer" class="d-flex w-100 gap-2">
            <input 
                type="text" 
                wire:model.defer="nouveauMessage" 
                placeholder="{{ \App\Helpers\TranslationHelper::TranslateText('Écrivez votre message...') }}" 
                class="form-control form-control-sm" 
                required>
            <button type="submit" class="btn btn-warning btn-sm text-white">
                <i class="fas fa-paper-plane"></i>
            </button>
        </form>
    </footer>
</div>
    </div>
    @endif

    <!-- Élément Audio pour le bip -->
    <audio id="chat-notification-sound" preload="auto">
        <source src="https://assets.mixkit.co/active_storage/sfx/2357/2357-84.wav" type="audio/wav">
    </audio>

    <!-- Script de gestion unique (Scroll, Bip et Notification SweetAlert2) -->
    <script>
        document.addEventListener('livewire:initialized', () => {
            // Configuration globale du Toast SweetAlert2 pour le client
            const ToastClient = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer);
                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                }
            });

            // Récupération dynamique des éléments du DOM
            const obtenirChatBox = () => document.getElementById('chat-messages-box');
            const audioPlayer = document.getElementById('chat-notification-sound');
            
            // Compteur de référence pour suivre les messages de l'administrateur
            let dernierNombreMessagesAdmin = obtenirNombreMessagesAdmin();

            function obtenirNombreMessagesAdmin() {
                return document.querySelectorAll('.chat-bubble-container.admin-side').length;
            }

            const scrollBottom = () => {
                const box = obtenirChatBox();
                if (box) { 
                    box.scrollTop = box.scrollHeight; 
                }
            };

            // Premier défilement automatique à l'initialisation
            scrollBottom();

            // Écouteur global basé sur l'événement personnalisé Livewire (bip-client)
            Livewire.on('bip-client', () => {
                if (audioPlayer) {
                    audioPlayer.currentTime = 0;
                    audioPlayer.play().catch(e => console.log("Audio en attente d'interaction"));
                }
                
                ToastClient.fire({
                    icon: 'success',
                    title: "{{ \App\Helpers\TranslationHelper::TranslateText('Le support vous a répondu !') }}"
                });

                setTimeout(scrollBottom, 50);
            });

            // Sécurité de secours : Détection par Morphing (Utile avec wire:poll)
            Livewire.hook('morph.updated', () => {
                const nouveauNombreMessagesAdmin = obtenirNombreMessagesAdmin();

                // Si un nouveau message admin est détecté dans le flux HTML rendu
                if (nouveauNombreMessagesAdmin > dernierNombreMessagesAdmin) {
                    if (audioPlayer) {
                        audioPlayer.currentTime = 0; 
                        audioPlayer.play().catch(e => {});
                    }

                    ToastClient.fire({
                        icon: 'success',
                        title: "{{ \App\Helpers\TranslationHelper::TranslateText('Le support vous a répondu !') }}"
                    });

                    setTimeout(scrollBottom, 50);
                }
                
                dernierNombreMessagesAdmin = nouveauNombreMessagesAdmin;
            });

            // Écouteur standard pour vos propres envois
            Livewire.on('scroll-chat-bottom', () => {
                setTimeout(scrollBottom, 50);
            });
        });
    </script>
</div>