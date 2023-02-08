package com.APPEx.APPExkotlin.di.subcomponent.album

import android.content.Context
import android.support.v7.widget.LinearLayoutManager
import com.APPEx.APPExkotlin.di.ActivityModule
import com.APPEx.APPExkotlin.di.scope.ActivityScope
import com.APPEx.APPExkotlin.domain.interactor.GetAlbumDetailInteractor
import com.APPEx.APPExkotlin.domain.interactor.base.Bus
import com.APPEx.APPExkotlin.domain.interactor.base.InteractorExecutor
import com.APPEx.APPExkotlin.ui.adapter.TracksAdapter
import com.APPEx.APPExkotlin.ui.entity.mapper.AlbumDetailDataMapper
import com.APPEx.APPExkotlin.ui.entity.mapper.TrackDataMapper
import com.APPEx.APPExkotlin.ui.presenter.AlbumPresenter
import com.APPEx.APPExkotlin.ui.screens.album.AlbumActivity
import com.APPEx.APPExkotlin.ui.view.AlbumView
import dagger.Module
import dagger.Provides

@Module
class AlbumActivityModule(activity: AlbumActivity) : ActivityModule(activity) {

    @Provides @ActivityScope
    fun provideAlbumView(): AlbumView = activity as AlbumView

    @Provides @ActivityScope
    fun provideAlbumDataMapper() = AlbumDetailDataMapper()

    @Provides @ActivityScope
    fun provideTrackDataMapper() = TrackDataMapper()

    @Provides @ActivityScope
    fun provideLinearLayoutManager(context: Context) = LinearLayoutManager(context)

    @Provides @ActivityScope
    fun provideTracksAdapter() = TracksAdapter()

    @Provides @ActivityScope
    fun provideAlbumPresenter(view: AlbumView,
                              bus: Bus,
                              albumInteractor: GetAlbumDetailInteractor,
                              interactorExecutor: InteractorExecutor,
                              albumDetailDataMapper: AlbumDetailDataMapper)
            = AlbumPresenter(view, bus, albumInteractor,
            interactorExecutor, albumDetailDataMapper)
}