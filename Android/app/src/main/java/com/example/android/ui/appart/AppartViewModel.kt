package com.example.android.ui.appart

import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.ViewModel

class AppartViewModel : ViewModel() {
    private val _text = MutableLiveData<String>().apply {
        value = "This is Contacts Fragment"
    }
    val text: LiveData<String> = _text
}