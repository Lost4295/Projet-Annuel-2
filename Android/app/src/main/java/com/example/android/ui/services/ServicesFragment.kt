package com.example.android.ui.services

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
import androidx.navigation.findNavController
import com.android.volley.AuthFailureError
import com.android.volley.Request
import com.android.volley.Response
import com.android.volley.toolbox.JsonObjectRequest
import com.android.volley.toolbox.Volley
import com.example.android.R
import com.example.android.databinding.FragmentServicesBinding
import com.example.android.ui.appart.AppartAdapter
import com.example.android.ui.appart.MiniAppart
import com.google.android.material.snackbar.Snackbar
import org.json.JSONArray
import org.json.JSONException
import org.json.JSONObject

class ServicesFragment : Fragment() {

    private var _binding: FragmentServicesBinding? = null

    // This property is only valid between onCreateView and
    // onDestroyView.
    private val binding get() = _binding!!

    override fun onCreateView(
        inflater: LayoutInflater,
        container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View {
        _binding = FragmentServicesBinding.inflate(inflater, container, false)
        val root: View = binding.root
        return root
    }

    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        val spinner = view.findViewById<ProgressBar>(R.id.progressBar3)
        val textView: TextView = view.findViewById(R.id.tv_loading_serv)
        val btn = view.findViewById<Button>(R.id.btn_more_serv)
        val lv = view.findViewById<ListView>(R.id.lv_services)
        spinner.visibility = View.VISIBLE
        textView.visibility = View.VISIBLE
        btn.visibility = View.GONE
        lv.visibility = View.GONE
        var queue = Volley.newRequestQueue(requireContext())
        var shp = requireContext().getSharedPreferences("user", 0)
        val name = shp.getString("name", null)
        val id = shp.getString("id", null)
        var token = shp.getString("token", "" ).toString()
        var adapter : ServiceAdapter? = null
        lv.adapter = null
        var req3 : JsonObjectRequest = object : JsonObjectRequest(
            Request.Method.GET,
            "https://api.pariscaretakerservices.fr/services",
            null,
            Response.Listener{ content ->
                var jsonarr: JSONArray = content.getJSONArray("hydra:member")
                var jsoninfo: JSONObject = content.getJSONObject("hydra:view")
                var listapp:MutableList<Service>  = mutableListOf<Service>()
                for ( i in 0 .. jsonarr.length()-1){
                    var cur_jso =jsonarr.getJSONObject(i)
                    var imgarr: JSONArray =  cur_jso.getJSONArray("images")
                    var b = Service(cur_jso.getLong("id"),
                        cur_jso.getString("titre"),
                        cur_jso.getString("description"),
                        imgarr.get(0).toString(),
                        )
                    listapp.add(b)
                }
                var actID : String = jsoninfo.getString("@id")
                var last: String = jsoninfo.getString("hydra:last")
                var next: String = ""
                next = jsoninfo.getString("hydra:next")
                if (actID != last) {
                    btn.visibility = View.VISIBLE
                    spinner.visibility = View.GONE
                    textView.visibility = View.GONE
                    lv.visibility = View.VISIBLE
                    btn.setOnClickListener {
                        spinner.visibility = View.VISIBLE
                        textView.visibility = View.VISIBLE
                        btn.visibility = View.GONE
                        lv.visibility = View.GONE
                        var req4 : JsonObjectRequest = object : JsonObjectRequest(
                            Request.Method.GET,
                            "https://api.pariscaretakerservices.fr$next",
                            null,
                            Response.Listener{ content ->
                                var jsonarr: JSONArray = content.getJSONArray("hydra:member")
                                var jsoninfo: JSONObject = content.getJSONObject("hydra:view")
                                for ( i in 0 .. jsonarr.length()-1){
                                    var cur_jso =jsonarr.getJSONObject(i)
                                    var imgarr: JSONArray =  cur_jso.getJSONArray("images")
                                    var b = Service(cur_jso.getLong("id"),
                                        cur_jso.getString("titre"),
                                        cur_jso.getString("description"),
                                        imgarr.get(0).toString(),
                                    )
                                    listapp.add(b)
                                }
                                adapter = ServiceAdapter(requireContext(), listapp)
                                lv.adapter = adapter
                                btn.visibility = View.VISIBLE
                                lv.visibility = View.VISIBLE
                                spinner.visibility = View.GONE
                                textView.visibility = View.GONE
                                actID = jsoninfo.getString("@id")
                                last = jsoninfo.getString("hydra:last")
                                try {
                                    next = jsoninfo.getString("hydra:next")
                                } catch (e: JSONException){
                                    val snackbar = Snackbar.make(view, "No more services", Snackbar.LENGTH_LONG)
                                    snackbar.show()
                                    btn.visibility = View.GONE
                                    lv.visibility = View.VISIBLE
                                    spinner.visibility = View.GONE
                                    textView.visibility = View.GONE
                                }
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
                                var params: MutableMap<String, String> = mutableMapOf<String,String>() ;
                                var final = "Bearer $token"
                                params.put("Authorization",final)
                                params.put("Accept","*/*")
                                params.put("Content-Type","application/ld+json")

                                //..add other headers
                                return params
                            }
                        };
                        queue.add(req4)
                    }
                } else {
                    btn.setOnClickListener{
                        val snackbar = Snackbar.make(view, "No more services", Snackbar.LENGTH_LONG)
                        snackbar.show()
                        spinner.visibility = View.GONE
                        textView.visibility = View.GONE
                        btn.visibility = View.GONE
                        lv.visibility = View.VISIBLE
                    }
                }
                adapter = ServiceAdapter(requireContext(), listapp)
                lv.adapter = adapter
                spinner.visibility = View.GONE
                textView.visibility = View.GONE
                btn.visibility = View.VISIBLE
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