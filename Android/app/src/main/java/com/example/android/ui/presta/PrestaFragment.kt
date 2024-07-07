package com.example.android.ui.presta

import android.os.Bundle
import android.util.Log
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.Button
import android.widget.ListView
import android.widget.ProgressBar
import android.widget.TextView
import android.widget.Toast
import androidx.fragment.app.Fragment
import androidx.lifecycle.ViewModelProvider
import com.android.volley.AuthFailureError
import com.android.volley.Request
import com.android.volley.Response
import com.android.volley.toolbox.JsonObjectRequest
import com.android.volley.toolbox.Volley
import com.example.android.R
import com.example.android.databinding.FragmentPrestaBinding
import com.example.android.ui.services.Service
import com.example.android.ui.services.ServiceAdapter
import com.google.android.material.snackbar.Snackbar
import org.json.JSONArray
import org.json.JSONException
import org.json.JSONObject

class PrestaFragment : Fragment() {

    private var _binding: FragmentPrestaBinding? = null

    // This property is only valid between onCreateView and
    // onDestroyView.
    private val binding get() = _binding!!

    override fun onCreateView(
        inflater: LayoutInflater,
        container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View {
        _binding = FragmentPrestaBinding.inflate(inflater, container, false)
        val root: View = binding.root

        return root
    }

    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        val spinner = view.findViewById<ProgressBar>(R.id.progressBar4)
        val textView: TextView = view.findViewById(R.id.tv_loading_presta)
        val lv = view.findViewById<ListView>(R.id.lv_presta)
        spinner.visibility = View.VISIBLE
        textView.visibility = View.VISIBLE

        lv.visibility = View.GONE
        var queue = Volley.newRequestQueue(requireContext())
        var shp = requireContext().getSharedPreferences("user", 0)
        val name = shp.getString("name", null)
        val id = shp.getString("id", null)
        var token = shp.getString("token", "" ).toString()
        var adapter : PrestaAdapter? = null
        lv.adapter = null
        var req3 : JsonObjectRequest = object : JsonObjectRequest(
            Request.Method.GET,
            "https://api.pariscaretakerservices.fr/getprestas",
            null,
            Response.Listener{ content ->
                var jsonarr: JSONArray = content.getJSONArray("data")
                var listapp:MutableList<Presta>  = mutableListOf<Presta>()
                for ( i in 0 .. jsonarr.length()-1){
                    var cur_jso =jsonarr.getJSONObject(i)
                    var b = Presta(cur_jso.getLong("id"),
                        cur_jso.getString("societyName"),
                        cur_jso.getString("image")
                    )
                    listapp.add(b)
                }
                adapter = PrestaAdapter(requireContext(), listapp)
                lv.adapter = adapter
                spinner.visibility = View.GONE
                textView.visibility = View.GONE
                lv.visibility = View.VISIBLE

            },
            Response.ErrorListener { error ->
                if (error.networkResponse != null) {
                    val statusCode = error.networkResponse.statusCode
                    val errorMessage = String(error.networkResponse.data, charset("UTF-8"))
                    Log.e("hhn", errorMessage)
                    Toast.makeText(requireContext(), "Error: $statusCode - $errorMessage", Toast.LENGTH_LONG).show()
                } else {
                    //Toast.makeText(requireContext(), error.message, Toast.LENGTH_LONG).show()
                    Toast.makeText(requireContext(), error.toString(), Toast.LENGTH_LONG).show()
                    Log.e("herhdertfee", error.toString())
                }
            }
        ){
            @Throws(AuthFailureError::class)
            override fun getHeaders(): Map<String, String> {
                val params: MutableMap<String, String> = mutableMapOf<String,String>() ;
                val final = "Bearer $token"
                params["Authorization"] = final
                params["Accept"] = "*/*"
                params["Content-Type"] = "application/ld+json"

                //..add other headers
                return params
            }
        }
        queue.add(req3)

        super.onViewCreated(view, savedInstanceState)
    }

    override fun onDestroyView() {
        super.onDestroyView()
        _binding = null
    }
}