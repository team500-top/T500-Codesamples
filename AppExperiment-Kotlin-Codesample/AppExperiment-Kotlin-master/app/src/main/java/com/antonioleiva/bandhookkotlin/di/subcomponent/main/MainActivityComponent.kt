package com.APPEx.APPExkotlin.di.subcomponent.main

import com.APPEx.APPExkotlin.di.scope.ActivityScope
import com.APPEx.APPExkotlin.ui.screens.main.MainActivity
import dagger.Subcomponent

@ActivityScope
@Subcomponent(modules = [(MainActivityModule::class)])
interface MainActivityComponent {

    fun injectTo(activity: MainActivity)
}