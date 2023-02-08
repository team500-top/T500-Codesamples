package com.APPEx.APPExkotlin.di

import com.APPEx.APPExkotlin.di.subcomponent.album.AlbumActivityComponent
import com.APPEx.APPExkotlin.di.subcomponent.album.AlbumActivityModule
import com.APPEx.APPExkotlin.di.subcomponent.detail.ArtistActivityComponent
import com.APPEx.APPExkotlin.di.subcomponent.detail.ArtistActivityModule
import com.APPEx.APPExkotlin.di.subcomponent.main.MainActivityComponent
import com.APPEx.APPExkotlin.di.subcomponent.main.MainActivityModule
import dagger.Component
import javax.inject.Singleton

@Singleton
@Component(
    modules = [(ApplicationModule::class), (DataModule::class), (RepositoryModule::class),
        (DomainModule::class)]
)
interface ApplicationComponent {

    fun plus(module: MainActivityModule): MainActivityComponent
    fun plus(module: ArtistActivityModule): ArtistActivityComponent
    fun plus(module: AlbumActivityModule): AlbumActivityComponent
}