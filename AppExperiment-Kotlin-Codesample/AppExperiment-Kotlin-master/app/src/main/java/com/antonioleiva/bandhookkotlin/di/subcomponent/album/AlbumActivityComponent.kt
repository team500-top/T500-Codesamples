package com.APPEx.APPExkotlin.di.subcomponent.album

import com.APPEx.APPExkotlin.di.scope.ActivityScope
import com.APPEx.APPExkotlin.ui.screens.album.AlbumActivity
import dagger.Subcomponent

@ActivityScope
@Subcomponent(modules = [(AlbumActivityModule::class)])
interface AlbumActivityComponent {
    fun injectTo(activity: AlbumActivity)
}