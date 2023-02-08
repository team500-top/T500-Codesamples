/*
 * Copyright (C) 2015 APPEx APPEx
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *       http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

package com.APPEx.APPExkotlin.ui.screens.main

import android.os.Bundle
import android.view.View
import com.APPEx.APPExkotlin.di.ApplicationComponent
import com.APPEx.APPExkotlin.di.subcomponent.main.MainActivityModule
import com.APPEx.APPExkotlin.ui.activity.BaseActivity
import com.APPEx.APPExkotlin.ui.adapter.BaseAdapter
import com.APPEx.APPExkotlin.ui.adapter.ImageTitleAdapter
import com.APPEx.APPExkotlin.ui.entity.ImageTitle
import com.APPEx.APPExkotlin.ui.presenter.MainPresenter
import com.APPEx.APPExkotlin.ui.screens.detail.ArtistActivity
import com.APPEx.APPExkotlin.ui.util.navigate
import com.APPEx.APPExkotlin.ui.view.MainView
import javax.inject.Inject

class MainActivity : BaseActivity<MainLayout>(), MainView {

    override val ui = MainLayout()

    @Inject
    lateinit var presenter: MainPresenter

    val adapter = ImageTitleAdapter { presenter.onArtistClicked(it) }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        ui.recycler.adapter = adapter
    }

    override fun injectDependencies(applicationComponent: ApplicationComponent) {
        applicationComponent.plus(MainActivityModule(this))
                .injectTo(this)
    }

    override fun onResume() {
        super.onResume()
        presenter.onResume()
    }

    override fun onPause() {
        super.onPause()
        presenter.onPause()
    }

    override fun showArtists(artists: List<ImageTitle>) {
        adapter.items = artists
    }

    override fun navigateToDetail(id: String) {
        navigate<ArtistActivity>(id, findItemById(id), BaseActivity.IMAGE_TRANSITION_NAME)
    }

    private fun findItemById(id: String): View {
        val pos = adapter.findPositionById(id)
        val holder = ui.recycler.findViewHolderForLayoutPosition(pos)
                as BaseAdapter.BaseViewHolder<ImageTitleAdapter.Component>
        return holder.ui.image
    }
}