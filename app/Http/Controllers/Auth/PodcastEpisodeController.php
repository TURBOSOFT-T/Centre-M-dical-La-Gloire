<?php

namespace App\Http\Controllers\Account;

use App\Helpers\TokenAbility;
use App\Helpers\UploadFilePath;
use App\Http\Controllers\Controller;
use App\Http\Requests\Account\Podcasts\CreatePodcastRequest;
use App\Http\Requests\Account\Podcasts\UpdatePodcastRequest;
use App\Http\Requests\Episode\CreatePodcastEpisodeRequest;
use App\Http\Requests\Episode\EpisodeRequest;
use App\Http\Requests\SearchPaginationRequest;
use App\Models\PodcastEpisode;
use App\Models\Podcast;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PodcastEpisodeController extends Controller
{


	/**
	 * Update podcast
	 *
	 * @Response({
	 *      code: 404
	 *      description: podcast not found
	 * })
	 * @Response({
	 *      code: 403
	 *      description: Not authorized
	 * })
	 *
	 * @Respone({
	 *      code: 200
	 *      ref: Podcast
	 * })
	 */

	/**
	 * create new podcast
	 *
	 * @Response({
	 * 		code: 200
	 * 		ref: episode
	 * })
	 */
	public function createEpisode(EpisodeRequest $request, $podcastUuid)
	{
		$user = Auth::id();
		$podcast = Podcast::findByUuidOrFail($podcastUuid);

		/** @var Episode $episode */
		$episode = new PodcastEpisode();

		$episode->uuid = Str::uuid()->toString();
		$episode->title = $request->title;
		$episode->producer = $request->producer;
		$episode->user_id = $user;
		$episode->description = $request->description;
		$episode->subjects = json_encode($request->subjects);
		$episode->label = $request->label;
		$episode->author_right_p_line = $request->author_right_p_line;
		$episode->author_right_c_line = $request->author_right_c_line;
		$episode->output_date = $request->output_date;
		$episode->catalogue_number = $request->catalogue_number;
		$episode->etiquette_name = $request->etiquette_name;
		$episode->version = $request->version;
		$episode->distributor = $request->distributor;
		$episode->author = $request->author;
		$episode->isni_author = $request->isni_author;
		$episode->ipi_author = $request->ipi_author;
		$episode->publisher = $request->publisher;
		$episode->isni_publisher = $request->isni_publisher;
		$episode->ipi_publisher = $request->ipi_publisher;
		$episode->music_compositor = $request->music_compositor;
		$episode->isni_music_compositor = $request->isni_music_compositor;
		$episode->ipi_music_compositor = $request->ipi_music_compositor;
		$episode->iswc = $request->iswc;
		$episode->podcast_id = $podcast->id;


        if ($request->file('audio_file')) {
            $audio       = !empty($request->file('audio_file')) ? $request->file('audio_file')->getClientOriginalName() : '';
            $request->file('audio_file')->move(UploadFilePath::PODCASTS_EPISODES_audios->value, $audio);
        }

        $episode->audio_file        = isset($audio) && !empty($audio) ? $audio : '';

		$episode->save();

		return response()->json($episode, Response::HTTP_CREATED);
	}


	public function deleteEpisode($podcastUuid, $episodeUuid)
	{
		$episode = PodcastEpisode::findByUuidOrFail($episodeUuid);
		if (Auth::check() && $episode->user_id == Auth::id()) {
			$episode->delete();
			return response()->noContent();
		} else {
			abort('You are not allowed to delete this podcast episode', Response::HTTP_FORBIDDEN);
		}
	}

    Route::prefix('episodes')->group(function () {

		Route::post('createEpisode', [PodcastEpisodeController::class, 'createEpisode']);



	});


}
