package com.example.android.ui.documents

import android.content.Context
import android.content.Intent
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.BaseAdapter
import android.widget.Button
import android.widget.TextView
import androidx.core.content.ContentProviderCompat.requireContext
import androidx.core.content.ContextCompat.startActivity
import com.example.android.R

class DocumentAdapter : BaseAdapter {
    var context: Context
    var documents: MutableList<Document>

    constructor(context: Context, documents: MutableList<Document>) : super() {
        this.context = context
        this.documents = documents
    }

    override fun getCount(): Int {
        return documents.size
    }

    override fun getItem(position: Int): Any {
        return  documents.get(position)
    }

    override fun getItemId(position: Int): Long {
        return documents.get(position).id
    }

    override fun getView(position: Int, convertView: View?, parent: ViewGroup?): View {
        val v: View
        if (convertView==null) {
            var inflater = LayoutInflater.from(this.context)
            v = inflater.inflate(R.layout.document_block,null)
        } else {
            v=convertView
        }
        var current: Document = getItem(position) as Document

        v.findViewById<TextView>(R.id.docname).text = current.name
        v.findViewById<TextView>(R.id.docurl).text = current.url
        val btn = v.findViewById<Button>(R.id.btndoc)
        btn.setOnClickListener {
            val url = current.url
            val intent = Intent(Intent.ACTION_VIEW)
            intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK);
            intent.data = android.net.Uri.parse("https://pariscaretakerservices.fr/files/pdfs/"+url)
            context.startActivity(intent)
        }
        return v
    }
}