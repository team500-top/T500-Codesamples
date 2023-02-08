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

package com.APPEx.APPExkotlin.domain.interactor

import com.APPEx.APPExkotlin.domain.interactor.base.Event
import com.APPEx.APPExkotlin.domain.interactor.base.Interactor
import com.APPEx.APPExkotlin.domain.interactor.event.ArtistDetailEvent
import com.APPEx.APPExkotlin.domain.repository.ArtistRepository

class GetArtistDetailInteractor(private val artistRepository: ArtistRepository) : Interactor {

    var id: String? = null

    override fun invoke(): Event {
        val id = this.id ?: throw IllegalStateException("id can´t be null")
        val artist = artistRepository.getArtist(id)
        return ArtistDetailEvent(artist)
    }
}