package com.example.android.ui.data

import android.content.Context
import android.util.Log
import android.widget.Toast
import com.android.volley.AuthFailureError
import com.android.volley.Request
import com.android.volley.Response
import com.android.volley.toolbox.JsonArrayRequest
import com.android.volley.toolbox.JsonObjectRequest
import com.android.volley.toolbox.Volley
import com.example.android.MiniAppart
import com.example.android.ui.data.model.LoggedInUser
import org.json.JSONArray
import org.json.JSONException
import org.json.JSONObject
import java.io.IOException

/**
 * Class that handles authentication w/ login credentials and retrieves user information.
 */
class LoginDataSource {

    fun login(username: String, password: String, context: Context): Result<LoggedInUser> {
        try {
            // TODO: handle loggedInUser authentication
            var queue = Volley.newRequestQueue(context)
            var token: String = ""
            var id: String = ""
            var name: String = ""
            val jsonBody = JSONObject()
            try {
                jsonBody.put("email", username)
                jsonBody.put("password", password)
            } catch (e: JSONException) {
                e.printStackTrace()
            }
            val json2 = JSONObject()
            json2.put("Authorization", "Bearer $token")
            val req2 : JsonObjectRequest = object : JsonObjectRequest(
                Request.Method.GET,
                "https://api.pariscaretakerservices.fr/user/",
                json2,
                Response.Listener{ content ->
                    try {
                        var cur_jso =content.getJSONArray("data")
                        if (cur_jso.getString(1) == username){
                            id = cur_jso.getString(0)
                            name = cur_jso.getString(1)
                        } else {
                            Toast.makeText(context, "Error: Data ; username = $username, name = $name", Toast.LENGTH_LONG).show()
                        }
                    if (id == ""){
                        Toast.makeText(context, "Error: User not found", Toast.LENGTH_LONG).show()
                    }
                    } catch (e: JSONException){
                        e.printStackTrace()
                    }
                },
                Response.ErrorListener { error ->
                    if (error.networkResponse != null) {
                        val statusCode = error.networkResponse.statusCode
                        val errorMessage = String(error.networkResponse.data, charset("UTF-8"))
                        Toast.makeText(context, "Error: $statusCode - $errorMessage", Toast.LENGTH_LONG).show()
                    } else {
                        Toast.makeText(context, error.toString(), Toast.LENGTH_LONG).show()
                    }
                    Log.e("herhe", error.toString())
                }
            ){
                @Throws(AuthFailureError::class)
                override fun getHeaders(): Map<String, String> {
                    var params: MutableMap<String, String> = mutableMapOf()
                    params.put("Authorization","Bearer $token")
                    params.put("Accept","*/*")
                    params.put("Content-Type","application/ld+json")
                    //..add other headers
                    return params
                }
            };
            var auth = JsonObjectRequest(
                Request.Method.POST,
                "https://api.pariscaretakerservices.fr/auth",
                jsonBody,
                Response.Listener{ content ->
                    try {
                        token = content.getString("token")
                        queue.add(req2)
                        //Toast.makeText(context, "WIN ! YOU ARE CONNECTED", Toast.LENGTH_LONG).show()
                    } catch (e: JSONException){
                        e.printStackTrace()
                    }
                },
                Response.ErrorListener { error ->
                    if (error.networkResponse != null) {
                        val statusCode = error.networkResponse.statusCode
                        val errorMessage = String(error.networkResponse.data, charset("UTF-8"))
                        Toast.makeText(context, "Error: $statusCode - $errorMessage", Toast.LENGTH_LONG).show()
                    } else {
                        Toast.makeText(context, error.toString(), Toast.LENGTH_LONG).show()
                    }
                    Log.e("herhe", error.toString())
                    Log.e("herhem", String(error.networkResponse.data, charset("UTF-8")))

                }
            )
            queue.add(auth)
            val fakeUser = LoggedInUser(id, name)
            return Result.Success(fakeUser)
        } catch (e: Throwable) {
            return Result.Error(IOException("Error logging in", e))
        }
    }

    fun logout() {
        // TODO: revoke authentication
    }
}