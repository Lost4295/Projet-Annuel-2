package com.example.android.ui.documents

class Document {
    var id: Long
    var name: String
    var url: String

    constructor(id: Long, name: String, url: String) {
        this.id = id
        this.name = name
        this.url = url
    }
}