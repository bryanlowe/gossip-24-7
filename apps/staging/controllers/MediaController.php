<?php
namespace app\controllers;


interface MediaController
{
    /**
     * load story assets from the database and echos the result or returns the result as an array
     */
    public function actionAssets($id);

    /**
     * Deletes a story asset from the database and echos the result
     */
    public function actionDelete();

    /**
     * uploads files to the targeted folder
     */
    public function actionUpload();
}
