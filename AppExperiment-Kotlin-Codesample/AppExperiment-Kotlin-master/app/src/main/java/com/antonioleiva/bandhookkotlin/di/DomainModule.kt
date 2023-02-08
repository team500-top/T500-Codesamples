package com.APPEx.APPExkotlin.di

import com.APPEx.APPExkotlin.domain.interactor.GetAlbumDetailInteractor
import com.APPEx.APPExkotlin.domain.interactor.GetArtistDetailInteractor
import com.APPEx.APPExkotlin.domain.interactor.GetRecommendedArtistsInteractor
import com.APPEx.APPExkotlin.domain.interactor.GetTopAlbumsInteractor
import com.APPEx.APPExkotlin.domain.repository.AlbumRepository
import com.APPEx.APPExkotlin.domain.repository.ArtistRepository
import dagger.Module
import dagger.Provides

@Module
class DomainModule {

    @Provides
    fun provideRecommendedArtistsInteractor(artistRepository: ArtistRepository)
            = GetRecommendedArtistsInteractor(artistRepository)

    @Provides
    fun provideArtistDetailInteractor(artistRepository: ArtistRepository)
            = GetArtistDetailInteractor(artistRepository)

    @Provides
    fun provideTopAlbumsInteractor(albumRepository: AlbumRepository)
            = GetTopAlbumsInteractor(albumRepository)

    @Provides
    fun provideAlbumsDetailInteractor(albumRepository: AlbumRepository)
            = GetAlbumDetailInteractor(albumRepository)
}