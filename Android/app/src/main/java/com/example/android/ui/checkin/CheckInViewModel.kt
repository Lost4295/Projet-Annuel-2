package com.example.android.ui.checkin

import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.ViewModel

class CheckInViewModel : ViewModel() {

    private val _text = MutableLiveData<String>().apply {
        value = "This is Check In Fragment"
    }
    val text: LiveData<String> = _text
}