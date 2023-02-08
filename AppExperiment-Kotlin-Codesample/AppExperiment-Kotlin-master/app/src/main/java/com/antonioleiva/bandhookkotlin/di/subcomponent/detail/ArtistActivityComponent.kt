package com.APPEx.APPExkotlin.di.subcomponent.detail

import com.APPEx.APPExkotlin.di.scope.ActivityScope
import com.APPEx.APPExkotlin.ui.screens.detail.ArtistActivity
import dagger.Subcomponent

@ActivityScope
@Subcomponent(modules = [(ArtistActivityModule::class)])
interface ArtistActivityComponent {

    fun injectTo(activity: ArtistActivity)
}