package com.example.android.ui.login

import androidx.lifecycle.Observer
import androidx.lifecycle.ViewModelProvider
import androidx.annotation.StringRes
import androidx.fragment.app.Fragment
import android.os.Bundle
import android.text.Editable
import android.text.TextWatcher
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.view.inputmethod.EditorInfo
import android.widget.TextView
import android.widget.Toast
import androidx.navigation.findNavController
import com.example.android.MainActivity
import com.example.android.databinding.FragmentLoginBinding

import com.example.android.R
import com.google.android.material.navigation.NavigationView

class LoginFragment : Fragment() {

    private lateinit var loginViewModel: LoginViewModel
    private var _binding: FragmentLoginBinding? = null

    // This property is only valid between onCreateView and
    // onDestroyView.
    private val binding get() = _binding!!

    override fun onCreateView(
        inflater: LayoutInflater,
        container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View? {
        _binding = FragmentLoginBinding.inflate(inflater, container, false)



        return binding.root

    }

    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        super.onViewCreated(view, savedInstanceState)
        val prefs = requireContext().getSharedPreferences("user", 0)
        val token = prefs.getString("usrtoken", null)
        val id = prefs.getString("id", null)
        val rname = prefs.getString("rname", null)

        if ((token != null && id != null) || rname != null) {
            view.findNavController().navigate(R.id.nav_profile)
        }
        loginViewModel = ViewModelProvider(this, LoginViewModelFactory())
            .get(LoginViewModel::class.java)

        val usernameEditText = binding.username
        val passwordEditText = binding.password
        val loginButton = binding.login
        val loadingProgressBar = binding.loading
        loadingProgressBar.visibility = View.GONE
        usernameEditText.visibility = View.VISIBLE
        passwordEditText.visibility = View.VISIBLE
        loginButton.isEnabled = false
        loginViewModel.loginFormState.observe(viewLifecycleOwner,
            Observer { loginFormState ->
                if (loginFormState == null) {
                    return@Observer
                }
                loginButton.isEnabled = loginFormState.isDataValid
                loginFormState.usernameError?.let {
                    usernameEditText.error = getString(it)
                }
                loginFormState.passwordError?.let {
                    passwordEditText.error = getString(it)
                }
            })

        loginViewModel.loginResult.observe(viewLifecycleOwner,
            Observer { loginResult ->
                loginResult ?: return@Observer
                loginResult.error?.let {
                    showLoginFailed(it)
                }
                loginResult.success?.let {
                    updateUiWithUser(it)
                }
            })

        val afterTextChangedListener = object : TextWatcher {
            override fun beforeTextChanged(s: CharSequence, start: Int, count: Int, after: Int) {
                // ignore
            }

            override fun onTextChanged(s: CharSequence, start: Int, before: Int, count: Int) {
                // ignore
            }

            override fun afterTextChanged(s: Editable) {
                loginViewModel.loginDataChanged(
                    usernameEditText.text.toString(),
                    passwordEditText.text.toString()
                )
            }
        }
        usernameEditText.addTextChangedListener(afterTextChangedListener)
        passwordEditText.addTextChangedListener(afterTextChangedListener)
        passwordEditText.setOnEditorActionListener { _, actionId, _ ->
            if (actionId == EditorInfo.IME_ACTION_DONE) {
                loadingProgressBar.visibility = View.VISIBLE
                usernameEditText.visibility = View.GONE
                passwordEditText.visibility = View.GONE
                loginButton.isEnabled = false
                loginViewModel.login(
                    usernameEditText.text.toString(),
                    passwordEditText.text.toString(),
                    requireContext(),
                    view
                )
            }
            false
        }

        loginButton.setOnClickListener {
            loadingProgressBar.visibility = View.VISIBLE
            usernameEditText.visibility = View.GONE
            passwordEditText.visibility = View.GONE
            loginButton.isEnabled = false
            loginViewModel.login(
                usernameEditText.text.toString(),
                passwordEditText.text.toString(),
                requireContext(),
                view
            )
        }
    }

    private fun updateUiWithUser(model: LoggedInUserView) {
        /*
        val welcome = getString(R.string.welcome) + model.displayName
        // TODO : initiate successful logged in experience
        val appContext = context?.applicationContext ?: return
        Toast.makeText(appContext, welcome, Toast.LENGTH_LONG).show()
         */

    }

    public fun update() {

    }

    private fun showLoginFailed(@StringRes errorString: Int) {
        val usernameEditText = binding.username
        val passwordEditText = binding.password
        val loginButton = binding.login
        val loadingProgressBar = binding.loading
        usernameEditText.visibility = View.VISIBLE
        passwordEditText.visibility = View.VISIBLE
        loginButton.isEnabled = true
        loadingProgressBar.visibility = View.GONE
        val appContext = context?.applicationContext ?: return
        Toast.makeText(appContext, errorString, Toast.LENGTH_LONG).show()
    }

    override fun onDestroyView() {
        super.onDestroyView()
        _binding = null
    }
}