package com.APPEx.APPExkotlin.ui.activity

import android.support.v7.app.AppCompatActivity
import android.support.v7.widget.Toolbar
import org.jetbrains.anko.AnkoComponent

interface ActivityAnkoComponent<T : AppCompatActivity> : AnkoComponent<T> {
    val toolbar: Toolbar
}