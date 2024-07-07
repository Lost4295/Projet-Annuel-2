package com.example.android.ui.services

class Service {
    var id:Long
    var title:String
    var text:String
    var image:String

    constructor(id: Long, title: String, text: String, image: String) {
        this.id = id
        this.title = title
        this.text = text
        this.image = image
    }
}