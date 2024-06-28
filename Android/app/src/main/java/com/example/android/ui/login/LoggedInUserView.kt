package com.example.android.ui.login

/**
 * User details post authentication that is exposed to the UI
 */
data class LoggedInUserView(
    val displayName: String,
    val id:Int
    //... other data fields that may be accessible to the UI
)