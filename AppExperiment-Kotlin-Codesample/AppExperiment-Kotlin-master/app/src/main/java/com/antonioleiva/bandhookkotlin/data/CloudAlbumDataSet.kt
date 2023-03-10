/*
 * Copyright (C) 2016 Alexey Verein
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

package com.APPEx.APPExkotlin.data

import com.APPEx.APPExkotlin.data.lastfm.LastFmService
import com.APPEx.APPExkotlin.data.mapper.AlbumMapper
import com.APPEx.APPExkotlin.domain.entity.Album
import com.APPEx.APPExkotlin.repository.dataset.AlbumDataSet

class CloudAlbumDataSet(private val lastFmService: LastFmService) : AlbumDataSet {

    override fun requestAlbum(mbid: String): Album?
            = lastFmService.requestAlbum(mbid).unwrapCall { AlbumMapper().transform(album) }

    override fun requestTopAlbums(artistId: String?, artistName: String?): List<Album> {
        val mbid = artistId ?: ""
        val name = artistName ?: ""

        if (!mbid.isEmpty() || !name.isEmpty()) {
            return lastFmService.requestAlbums(mbid, name).unwrapCall {
                AlbumMapper().transform(topAlbums.albums)
            } ?: emptyList()
        }

        return emptyList()
    }
}
