package com.APPEx.APPExkotlin.di.subcomponent.main

import com.APPEx.APPExkotlin.di.ActivityModule
import com.APPEx.APPExkotlin.di.scope.ActivityScope
import com.APPEx.APPExkotlin.domain.interactor.GetRecommendedArtistsInteractor
import com.APPEx.APPExkotlin.domain.interactor.base.Bus
import com.APPEx.APPExkotlin.domain.interactor.base.InteractorExecutor
import com.APPEx.APPExkotlin.ui.adapter.ImageTitleAdapter
import com.APPEx.APPExkotlin.ui.entity.mapper.ImageTitleDataMapper
import com.APPEx.APPExkotlin.ui.presenter.MainPresenter
import com.APPEx.APPExkotlin.ui.screens.main.MainActivity
import com.APPEx.APPExkotlin.ui.view.MainView
import dagger.Module
import dagger.Provides

@Module
class MainActivityModule(activity: MainActivity) : ActivityModule(activity) {

    @Provides @ActivityScope
    fun provideMainView(): MainView = activity as MainView

    @Provides @ActivityScope
    fun provideImageTitleMapper() = ImageTitleDataMapper()

    @Provides @ActivityScope
    fun provideMainPresenter(view: MainView, bus: Bus,
                             recommendedArtistsInteractor: GetRecommendedArtistsInteractor,
                             interactorExecutor: InteractorExecutor,
                             imageMapper: ImageTitleDataMapper) = MainPresenter(view, bus, recommendedArtistsInteractor,
            interactorExecutor, imageMapper)
}