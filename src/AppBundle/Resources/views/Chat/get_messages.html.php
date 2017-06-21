<?php $view->extend('::base.html.twig') ?>

<?php $view['slots']->set('title', 'AppBundle:Chat:getMessages') ?>

<?php $view['slots']->start('body') ?>
    <h1>Welcome to the Chat:getMessages page</h1>
<?php $view['slots']->stop() ?>
