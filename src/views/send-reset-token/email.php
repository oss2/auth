
Hi,

We received a password reset request for your account.

If you wish to reset you password, please visit the following link
and use the token below.

<?php echo action( 'Oss2\Auth\Controller\Auth@getReset', [ 'username' => $user->getAuthIdentifier(), 'token' => $token ] ); ?>

Your token is <?php echo $token; ?>
