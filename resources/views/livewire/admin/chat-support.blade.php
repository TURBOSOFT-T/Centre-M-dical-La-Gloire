<div class="admin-chat-container" style="display: flex; height: calc(100vh - 150px); background: #f4f6f9; font-family: sans-serif;">

    <!-- BARRE LATÉRALE -->
    <div class="sidebar-conversations" style="width: 350px; background: #fff; border-right: 1px solid #e0e0e0; display: flex; flex-direction: column;">
        <div style="padding: 20px; border-bottom: 1px solid #e0e0e0; background: #2c3e50; color: #fff;">
            <h3 style="margin: 0; font-size: 18px;"><i class="fas fa-comments-alt"></i> Support SWOOT BIO</h3>
            <span style="font-size: 12px; opacity: 0.8;">Sessions actives</span>
        </div>

        <div style="flex: 1; overflow-y: auto;" wire:poll.5s>
            @if($conversations->isEmpty())
            <div style="padding: 40px; text-align: center; color: #888;">
                <p>Aucun visiteur sur le chat pour le moment.</p>
            </div>
            @else
            @foreach($conversations as $conv)
            @php
            $dernierMsg = \App\Models\Message::where('session_id', $conv->session_id)
                ->orderBy('created_at', 'desc')
                ->first();
            @endphp

            <div wire:click="selectionnerConversation('{{ $conv->session_id }}')"
                style="padding: 15px 20px; border-bottom: 1px solid #f0f0f0; cursor: pointer; transition: 0.2s; {{ $conversationSelectionneeId == $conv->session_id ? 'background: #e3f2fd; border-left: 4px solid #1e88e5;' : '' }}">

                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px;">
                    <strong style="color: #333;">
                        Visiteur #{{ substr($conv->session_id, 0, 6) }}
                    </strong>
                    <span style="font-size: 11px; color: #999;">{{ $conv->dernier_message_at ? \Carbon\Carbon::parse($conv->dernier_message_at)->format('H:i') : '' }}</span>
                </div>

                <div style="font-size: 11px; color: #aaa; margin-bottom: 5px; font-family: monospace;">
                    ID: {{ substr($conv->session_id, 0, 8) }}...
                </div>

                <div style="font-size: 13px; color: #666; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                    {{ $dernierMsg ? $dernierMsg->contenu : '' }}
                </div>
            </div>
            @endforeach
            @endif
        </div>
    </div>

    <!-- ZONE DE DISCUSSION -->
    <div class="chat-main-window" style="flex: 1; display: flex; flex-direction: column; background: #fdfdfd;">
        @if($conversationSelectionneeId)
        <!-- En-tête avec actions de gestion de session -->
        <div style="padding: 15px 25px; background: #fff; border-bottom: 1px solid #e0e0e0; display: flex; justify-content: space-between; align-items: center;">
            <h4 style="margin: 0; color: #2c3e50;">Session temporaire : {{ substr($conversationSelectionneeId, 0, 15) }}...</h4>

            <div style="display: flex; gap: 10px;">
                <!-- Bouton 1 : Supprimer uniquement les messages du client -->
                <button onclick="confirmerSuppressionClient()"
                    style="background: #ffc107; color: #212529; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; font-size: 13px; font-weight: bold; transition: 0.2s;">
                    <i class="fas fa-eraser"></i> Effacer messages client
                </button>

                <!-- Bouton 2 : Tout effacer -->
                <button onclick="confirmerToutEffacer()"
                    style="background: #dc3545; color: white; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; font-size: 13px; font-weight: bold; transition: 0.2s;">
                    <i class="fas fa-trash-alt"></i> Effacez tous les messages
                </button>
            </div>
        </div>

        <div id="admin-chat-box" style="flex: 1; padding: 25px; overflow-y: auto; background: #f7f9fa; display: flex; flex-direction: column; gap: 15px;" wire:poll.2s>
            @foreach($messages as $msg)
            <!-- Ligne de message -->
            <div class="chat-message-row" style="display: flex; width: 100%; align-items: center; gap: 10px; {{ $msg->expediteur === 'admin' ? 'justify-content: flex-end; flex-direction: row-reverse;' : 'justify-content: flex-start;' }}">

                <!-- Bulle -->
                <div style="max-width: 65%; padding: 12px 16px; border-radius: 12px;
                            {{ $msg->expediteur === 'admin' ? 'background: #007bff; color: #fff; border-bottom-right-radius: 2px;' : 'background: #fff; color: #333; border-bottom-left-radius: 2px; border: 1px solid #e4e6eb;' }}">
                    <div style="font-size: 14px; line-height: 1.4; word-break: break-word;">{{ $msg->contenu }}</div>
                    <div style="font-size: 10px; margin-top: 5px; text-align: right; opacity: 0.7;">{{ $msg->created_at->format('H:i') }}</div>
                </div>

                <!-- Action de suppression de message unique -->
                <button wire:click="supprimerMessageSpécifique({{ $msg->id }})"
                    wire:confirm="Voulez-vous vraiment supprimer ce message définitivement ?"
                    class="btn-delete-msg"
                    title="Supprimer ce message"
                    style="background: none; border: none; color: #dc3545; cursor: pointer; padding: 5px; font-size: 14px; transition: opacity 0.2s; opacity: 0; visibility: hidden;">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </div>
            @endforeach
        </div>

        <form wire:submit.prevent="envoyer" style="padding: 20px; background: #fff; border-top: 1px solid #e0e0e0; display: flex; gap: 10px;">
            <input type="text" wire:model="nouveauMessage" placeholder="Écrivez votre réponse..." style="flex: 1; padding: 12px 15px; border: 1px solid #ccc; border-radius: 6px;" required>
            <button type="submit" style="background: #007bff; color: #fff; border: none; padding: 0 25px; border-radius: 6px; cursor: pointer;">Envoyer</button>
        </form>
        @else
        <div style="flex: 1; display: flex; flex-direction: column; justify-content: center; align-items: center; color: #bbb;">
            <i class="fas fa-headset" style="font-size: 60px; margin-bottom: 15px;"></i>
            <h3>Aucune session sélectionnée</h3>
        </div>
        @endif
    </div>

    <!-- Élément Audio pour les notifications -->
    <audio id="admin-notif-sound" preload="auto">
        <source src="https://assets.mixkit.co/active_storage/sfx/2357/2357-84.wav" type="audio/wav">
    </audio>

    <!-- Styles CSS utilitaires -->
    <style>
        .chat-message-row:hover .btn-delete-msg {
            visibility: visible !important;
            opacity: 0.6 !important;
        }
        .chat-message-row .btn-delete-msg:hover {
            opacity: 1 !important;
        }
    </style>

    <!-- Inclusion unique de SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Script JavaScript unifié de gestion globale -->
    <script>
        // Fonctions de déclenchement global (accessibles par les attributs onclick)
        function confirmerSuppressionClient() {
            Swal.fire({
                title: 'Effacer les messages du client ?',
                text: "Tous les messages envoyés par ce client seront effacés. Vos réponses d'administrateur seront conservées.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ffc107',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-eraser"></i> Oui, effacer',
                cancelButtonText: 'Annuler',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // On appelle la méthode Livewire, le SweetAlert de succès sera géré par l'écouteur d'événement plus bas
                    @this.call('supprimerMessagesDuClient');
                }
            });
        }

        function confirmerToutEffacer() {
            Swal.fire({
                title: 'Tout effacer ?',
                text: 'Êtes-vous sûr de vouloir clore cette discussion ? Tous les messages associés seront définitivement effacés.',
                icon: 'error',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-trash-alt"></i> Oui, tout supprimer',
                cancelButtonText: 'Annuler',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // On appelle la méthode Livewire
                    @this.call('toutEffacer');
                }
            });
        }

        // Initialisation de la logique interne Livewire
        document.addEventListener('livewire:initialized', () => {
            const sound = document.getElementById('admin-notif-sound');
            let totalMessagesClient = compterMessagesClient();

            function compterMessagesClient() {
                return document.querySelectorAll('#admin-chat-box > div[style*="justify-content: flex-start"]').length;
            }

            const scrollAdminBottom = () => {
                const box = document.getElementById('admin-chat-box');
                if (box) {
                    box.scrollTop = box.scrollHeight;
                }
            };

            // Premier scroll au chargement initial
            scrollAdminBottom();

            // Écouteur de mise à jour HTML (Polling) pour jouer le son si nouveau message client
            Livewire.hook('morph.updated', () => {
                const nouveauTotalClient = compterMessagesClient();
                if (nouveauTotalClient > totalMessagesClient) {
                    if (sound) {
                        sound.currentTime = 0;
                        sound.play().catch(e => {});
                    }
                    scrollAdminBottom();
                }
                totalMessagesClient = nouveauTotalClient;
            });

            // Écouteur pour forcer le défilement vers le bas
            Livewire.on('scroll-chat-bottom', () => {
                setTimeout(scrollAdminBottom, 50);
            });

            // --- ÉCOUTEURS DES ÉVÉNEMENTS DU COMPOSANT PHP (SWEETALERT DE SUCCÈS) ---

            // Capturé après l'exécution réussie de supprimerMessagesDuClient()
            Livewire.on('client-messages-effaces', () => {
                Swal.fire({
                    title: 'Messages effacés !',
                    text: "Les messages envoyés par le client ont été supprimés.",
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });
            });

            // Capturé après l'exécution réussie de toutEffacer()
            Livewire.on('tout-a-ete-efface', () => {
                Swal.fire({
                    title: 'Discussion close !',
                    text: "L'historique complet de la session a été supprimé.",
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });
            });
        });
    </script>
</div>