<dl id="nowPlaying">
			<dt><?= albumPicture($currentSong) ?></dt>
			<dd><h2>Now Playing</h2><hr /></dd>
			<dd><?= songTitle($currentSong) ?> (<?= songTime($currentSong['duration']) ?>)</dd>
			<? if (!empty($songMessage['msg'])) { ?>
			<dd>Dedication: <?= htmlentities($songMessage['msg']) ?> by <span><?= htmlentities($songMessage['name']) ?></span></dd>
			<? } ?>
		</dl>