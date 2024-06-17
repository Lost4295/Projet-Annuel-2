package com.example.android.ui.presta

import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.ViewModel

class PrestaViewModel : ViewModel() {

    private val _text = MutableLiveData<String>().apply {
        value = "This is Presta Fragment"
    }
    val text: LiveData<String> = _text
}