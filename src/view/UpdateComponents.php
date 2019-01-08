<!-- UpdateComponents -->

<h3>Updated all software components to their latest version.</h3>
<small>Raises the versions of all software components of all installation wizards of the next release automatically.</small>

<?php echo $html; ?>

<div class="alert alert-success" role="alert">
    You might "git commit/push" now!<br> The commit message is: "<b>updated installer registries of "next" version</b>".
    <br>
    <a class="btn btn-success" href="index.php?action=GitPushNextVersionRegistries&gitpush=true" role="button">
    Git Commit, then Push
    </a>
</div>

<!-- /UpdateComponents -->