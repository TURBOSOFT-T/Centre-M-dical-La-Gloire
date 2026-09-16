<div>
    <div class="row">
        @forelse ($banners as $banner)
            <div class="col-sm-4 col-xl-3">
                <div class="list-banner-image card p-2">
                    <div class="image">
                        <button type="button" class="btn btn-sm btn-danger" wire:click="delete({{ $banner->id }})" wire:confirm="Supprimer ?">
                            <i class="ri-delete-bin-6-line"></i>
                        </button>
                        <img src="{{ Storage::url($banner->image) }}" alt="{{ $banner->titre }}" class="w-100" srcset="">
                    </div>
                    <h6 class="mt-1 mb-2 titre">
                        <b> {{ Str::limit($banner->titre , 50) }} </b>
                    </h6>
                    <p class="sous_titre">
                        {{ $banner->sous_titre ?? "" }}
                    </p>
                    <div class="text-center">
                        <a href="{{ route('banner.update',['id'=>$banner->id]) }}">
                            <i class="ri-edit-2-line"></i>
                            Modifier
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-sm-6 mx-auto text-center">
                <div>
                    <img width="100" height="100" src="https://img.icons8.com/ios/100/00c100/image-not-avialable.png" alt="image-not-avialable"/>
                </div>
                <h6>
                    Aucune Image !
                </h6>
            </div>
        @endforelse
    </div>
</div>
