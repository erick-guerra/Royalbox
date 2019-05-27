/*
 * Copyright (C) 2012 The Android Open Source Project
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not use this file except
 * in compliance with the License. You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software distributed under the License
 * is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express
 * or implied. See the License for the specific language governing permissions and limitations under
 * the License.
 */

package com.gplexdb.royalboxremote;

/**
 *
 * A set of constants used by all of the components in this application. To use these constants
 * the components implement the interface.
 */

public final class CommonConstants {

    public CommonConstants() {

        // don't allow the class to be instantiated
    }

    public static String IP = "com.gplexdb.royalboxremote.IP";
    public static final String EXTRA_MESSAGE= "com.gplexdb.royalboxremote.EXTRA_MESSAGE";
    public static final String ACTION_START= "com.gplexdb.royalboxremote.ACTION_START";
    public static final String ACTION_STOP= "com.gplexdb.royalboxremote.ACTION_STOP";
    public static final String ACTION_PAUSE = "com.gplexdb.royalboxremote.ACTION_PAUSE";
    public static final String ACTION_REVIND = "com.gplexdb.royalboxremote.ACTION_REVIND";
    public static final String ACTION_FAST_FORWARD = "com.gplexdb.royalboxremote.ACTION_FAST_FORWARD";
    public static final int NOTIFICATION_ID = 001;
}
