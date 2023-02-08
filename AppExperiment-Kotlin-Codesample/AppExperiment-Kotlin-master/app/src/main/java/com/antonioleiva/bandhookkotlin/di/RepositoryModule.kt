package com.APPEx.APPExkotlin.di

import com.APPEx.APPExkotlin.data.CloudAlbumDataSet
import com.APPEx.APPExkotlin.data.CloudArtistDataSet
import com.APPEx.APPExkotlin.data.lastfm.LastFmService
import com.APPEx.APPExkotlin.di.qualifier.LanguageSelection
import com.APPEx.APPExkotlin.domain.repository.AlbumRepository
import com.APPEx.APPExkotlin.domain.repository.ArtistRepository
import com.APPEx.APPExkotlin.repository.AlbumRepositoryImpl
import com.APPEx.APPExkotlin.repository.ArtistRepositoryImpl
import dagger.Module
import dagger.Provides
import javax.inject.Singleton

@Module
class RepositoryModule {

    @Provides @Singleton
    fun provideArtistRepo(@LanguageSelection language: String, lastFmService: LastFmService): ArtistRepository
            = ArtistRepositoryImpl(listOf(CloudArtistDataSet(language, lastFmService)))

    @Provides @Singleton
    fun provideAlbumRepo(lastFmService: LastFmService): AlbumRepository
            = AlbumRepositoryImpl(listOf(CloudAlbumDataSet(lastFmService)))
}