package com.APPEx.APPExkotlin.di.subcomponent.detail

import com.APPEx.APPExkotlin.di.ActivityModule
import com.APPEx.APPExkotlin.di.scope.ActivityScope
import com.APPEx.APPExkotlin.domain.interactor.GetArtistDetailInteractor
import com.APPEx.APPExkotlin.domain.interactor.GetTopAlbumsInteractor
import com.APPEx.APPExkotlin.domain.interactor.base.Bus
import com.APPEx.APPExkotlin.domain.interactor.base.InteractorExecutor
import com.APPEx.APPExkotlin.ui.entity.mapper.ArtistDetailDataMapper
import com.APPEx.APPExkotlin.ui.entity.mapper.ImageTitleDataMapper
import com.APPEx.APPExkotlin.ui.presenter.ArtistPresenter
import com.APPEx.APPExkotlin.ui.screens.detail.AlbumsFragment
import com.APPEx.APPExkotlin.ui.screens.detail.ArtistActivity
import com.APPEx.APPExkotlin.ui.screens.detail.BiographyFragment
import com.APPEx.APPExkotlin.ui.view.ArtistView
import dagger.Module
import dagger.Provides

@Module
class ArtistActivityModule(activity: ArtistActivity) : ActivityModule(activity) {

    @Provides @ActivityScope
    fun provideArtistView(): ArtistView = activity as ArtistView

    @Provides @ActivityScope
    fun provideArtistDataMapper() = ArtistDetailDataMapper()

    @Provides @ActivityScope
    fun provideImageTitleDataMapper() = ImageTitleDataMapper()

    @Provides @ActivityScope
    fun provideActivityPresenter(view: ArtistView,
                                 bus: Bus,
                                 artistDetailInteractor: GetArtistDetailInteractor,
                                 topAlbumsInteractor: GetTopAlbumsInteractor,
                                 interactorExecutor: InteractorExecutor,
                                 detailDataMapper: ArtistDetailDataMapper,
                                 imageTitleDataMapper: ImageTitleDataMapper)
            = ArtistPresenter(view, bus, artistDetailInteractor, topAlbumsInteractor,
            interactorExecutor, detailDataMapper, imageTitleDataMapper)

    @Provides @ActivityScope
    fun provideAlbumsFragment() = AlbumsFragment()

    @Provides @ActivityScope
    fun provideBiographyFragment() = BiographyFragment()
}