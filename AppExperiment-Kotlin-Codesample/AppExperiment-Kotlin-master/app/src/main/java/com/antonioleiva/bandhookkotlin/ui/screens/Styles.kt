package com.APPEx.APPExkotlin.ui.screens

import android.view.View
import com.APPEx.APPExkotlin.R
import com.APPEx.APPExkotlin.ui.custom.AutofitRecyclerView
import com.APPEx.APPExkotlin.ui.custom.PaddingItemDecoration
import org.jetbrains.anko.*


fun AutofitRecyclerView.style() {
    clipToPadding = false
    columnWidth = dimen(R.dimen.column_width)
    scrollBarStyle = View.SCROLLBARS_OUTSIDE_OVERLAY
    horizontalPadding = dimen(R.dimen.recycler_spacing)
    verticalPadding = dip(2)
    addItemDecoration(PaddingItemDecoration(dip(2)))
}