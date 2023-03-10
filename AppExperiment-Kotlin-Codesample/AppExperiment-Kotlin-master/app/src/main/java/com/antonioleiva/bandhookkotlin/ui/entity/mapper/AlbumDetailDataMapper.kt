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

package com.APPEx.APPExkotlin.ui.entity.mapper

import com.APPEx.APPExkotlin.domain.entity.Album
import com.APPEx.APPExkotlin.ui.entity.AlbumDetail

class AlbumDetailDataMapper {

    fun transform(album: Album?) = album?.let { AlbumDetail(
            album.id,
            album.name,
            album.url,
            album.tracks)
    }
}